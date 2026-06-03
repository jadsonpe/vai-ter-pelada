<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaJogoParticipanteEstatistica;
use App\Models\PeladaMembro;
use App\Models\PlayerProfile;
use App\Models\PlayerStat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JogoController extends Controller
{
    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);
        $this->finalizarRodadasExpiradas($pelada);

        return view('organizador.jogos.index', [
            'pelada' => $pelada->load([
                'jogos' => fn ($query) => $query->orderByDesc('data_hora')->orderByDesc('id'),
                'jogos.participantes.user',
            ]),
        ]);
    }

    public function show(PeladaJogo $jogo): View
    {
        $this->authorizeOwner($jogo->pelada);
        $this->finalizarSeExpirada($jogo);
        $jogo->refresh();

        $jogo->load([
            'pelada.membros.user',
            'participantes.user',
            'participantes.membro.user',
            'participantes.estatistica',
            'sorteios.times.jogadores.participante.membro',
            'sorteios.times.jogadores.participante.user',
            'sorteios.times.jogadores.user',
            'sorteios.sobras',
        ]);

        $usuariosJaListados = $jogo->participantes
            ->whereIn('status', ['confirmado', 'fila'])
            ->pluck('user_id');

        return view('organizador.jogos.show', [
            'jogo' => $jogo,
            'membrosDisponiveis' => $jogo->pelada->membros
                ->where('status', 'ativo')
                ->whereNotIn('user_id', $usuariosJaListados)
                ->sortBy(fn (PeladaMembro $membro) => $membro->nomeExibicao()),
            'confirmados' => $jogo->participantes
                ->where('status', 'confirmado')
                ->sortBy(fn (PeladaJogoParticipante $participante) => $participante->ordem_presenca ?? $participante->ordem_chegada ?? $participante->id)
                ->values(),
            'ultimoSorteio' => $jogo->sorteios->sortByDesc('created_at')->first(),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);

        $data = $request->validate([
            'data_hora' => ['required', 'date', 'after:now'],
            'vagas_totais' => ['nullable', 'integer', 'min:2'],
            'observacao' => ['nullable'],
        ]);

        $data['titulo'] = $this->proximoTituloRodada($pelada);
        $data['data_jogo'] = date('Y-m-d', strtotime($data['data_hora']));
        $data['horario'] = date('H:i:s', strtotime($data['data_hora']));
        $data['capacidade'] = $data['vagas_totais'] ?: $pelada->vagas_totais;
        $data['vagas_diaristas'] = 0;

        $pelada->jogos()->create($data + ['status' => 'aberto']);

        return back()->with('status', 'Rodada criada.');
    }

    public function update(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);
        $this->bloquearSeFinalizada($jogo);

        $data = $request->validate([
            'data_hora' => ['required', 'date'],
            'vagas_totais' => ['nullable', 'integer', 'min:2'],
            'vagas_diaristas' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:aberto,fechado,finalizado,cancelado,realizado'],
            'observacao' => ['nullable'],
        ]);

        $data['data_jogo'] = date('Y-m-d', strtotime($data['data_hora']));
        $data['horario'] = date('H:i:s', strtotime($data['data_hora']));
        $data['capacidade'] = $data['vagas_totais'] ?: $jogo->pelada->vagas_totais;

        $jogo->update($data);

        return back()->with('status', 'Rodada atualizada.');
    }

    public function participantes(PeladaJogo $jogo): View
    {
        return $this->show($jogo);
    }

    public function confirmarMembro(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);
        $this->bloquearSeFinalizada($jogo);

        $data = $request->validate([
            'pelada_membro_id' => ['required', 'integer'],
        ]);

        $membro = $jogo->pelada->membros()
            ->where('id', $data['pelada_membro_id'])
            ->where('status', 'ativo')
            ->firstOrFail();

        PeladaJogoParticipante::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'user_id' => $membro->user_id],
            [
                'pelada_membro_id' => $membro->id,
                'tipo' => $membro->tipo,
                'tipo_no_jogo' => $membro->tipo,
                'status' => 'confirmado',
                'ordem_chegada' => (int) $jogo->participantes()->max('ordem_chegada') + 1,
                'posicao_fila' => null,
                'confirmado_em' => now(),
                'cancelado_em' => null,
            ]
        );

        return back()->with('status', 'Membro confirmado na rodada.');
    }

    public function salvarEstatisticas(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);
        $this->bloquearSeFinalizada($jogo);

        $data = $request->validate([
            'participantes' => ['nullable', 'array'],
            'participantes.*.gols' => ['nullable', 'integer', 'min:0', 'max:99'],
            'participantes.*.cartoes_amarelos' => ['nullable', 'integer', 'min:0', 'max:9'],
            'participantes.*.cartoes_vermelhos' => ['nullable', 'integer', 'min:0', 'max:9'],
            'participantes.*.cartoes_azuis' => ['nullable', 'integer', 'min:0', 'max:9'],
            'participantes.*.observacao' => ['nullable', 'string', 'max:500'],
        ]);

        $participantes = $jogo->participantes()
            ->whereIn('status', ['confirmado', 'fila'])
            ->get()
            ->keyBy('id');

        DB::transaction(function () use ($data, $jogo, $participantes) {
            foreach ($data['participantes'] ?? [] as $participanteId => $payload) {
                $participante = $participantes->get((int) $participanteId);

                if (! $participante) {
                    continue;
                }

                PeladaJogoParticipanteEstatistica::updateOrCreate(
                    ['pelada_jogo_participante_id' => $participante->id],
                    [
                        'pelada_jogo_id' => $jogo->id,
                        'user_id' => $participante->user_id,
                        'gols' => (int) ($payload['gols'] ?? 0),
                        'cartoes_amarelos' => (int) ($payload['cartoes_amarelos'] ?? 0),
                        'cartoes_vermelhos' => (int) ($payload['cartoes_vermelhos'] ?? 0),
                        'cartoes_azuis' => (int) ($payload['cartoes_azuis'] ?? 0),
                        'observacao' => $payload['observacao'] ?? null,
                    ]
                );
            }

            $this->atualizarGolsDosUsuarios($jogo);
        });

        return back()->with('status', 'Estatisticas da rodada salvas.');
    }

    public function removerParticipante(PeladaJogo $jogo, PeladaJogoParticipante $participante): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);
        $this->bloquearSeFinalizada($jogo);
        abort_unless($participante->pelada_jogo_id === $jogo->id, 404);

        $eraConfirmado = $participante->status === 'confirmado';

        $participante->update([
            'status' => 'cancelado',
            'cancelado_em' => now(),
            'posicao_fila' => null,
        ]);

        if ($eraConfirmado) {
            $this->promoverFila($jogo);
        }

        return back()->with('status', 'Participante removido da rodada.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaOwner($pelada);
    }

    private function proximoTituloRodada(Pelada $pelada): string
    {
        return 'Rodada '.($pelada->jogos()->count() + 1);
    }

    private function promoverFila(PeladaJogo $jogo): void
    {
        $proximo = $jogo->participantes()
            ->where('status', 'fila')
            ->orderByRaw("tipo = 'mensalista' desc")
            ->orderBy('ordem_chegada')
            ->orderBy('posicao_fila')
            ->first();

        if ($proximo) {
            $proximo->update([
                'status' => 'confirmado',
                'posicao_fila' => null,
                'confirmado_em' => now(),
            ]);
        }
    }

    private function finalizarRodadasExpiradas(Pelada $pelada): void
    {
        $pelada->jogos()
            ->where('data_hora', '<=', now()->subDay())
            ->whereIn('status', ['aberto', 'fechado', 'realizado'])
            ->update(['status' => 'finalizado']);
    }

    private function finalizarSeExpirada(PeladaJogo $jogo): void
    {
        if ($jogo->prazoEdicaoEncerrado() && in_array($jogo->status, ['aberto', 'fechado', 'realizado'], true)) {
            $jogo->update(['status' => 'finalizado']);
        }
    }

    private function bloquearSeFinalizada(PeladaJogo $jogo): void
    {
        $this->finalizarSeExpirada($jogo);
        $jogo->refresh();

        if ($jogo->bloqueadoParaEdicao()) {
            abort(403, 'Rodada finalizada. Edicoes nao estao mais disponiveis.');
        }
    }

    private function atualizarGolsDosUsuarios(PeladaJogo $jogo): void
    {
        $userIds = $jogo->estatisticas()
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if (! $user) {
                continue;
            }

            $profile = PlayerProfile::ensureForUser($user);

            $gols = PeladaJogoParticipanteEstatistica::query()
                ->where('user_id', $userId)
                ->whereHas('jogo.pelada', fn ($query) => $query->where('esporte_id', $jogo->pelada->esporte_id))
                ->sum('gols');

            $jogos = PeladaJogoParticipanteEstatistica::query()
                ->where('user_id', $userId)
                ->whereHas('jogo.pelada', fn ($query) => $query->where('esporte_id', $jogo->pelada->esporte_id))
                ->distinct('pelada_jogo_id')
                ->count('pelada_jogo_id');

            PlayerStat::updateOrCreate(
                [
                    'player_profile_id' => $profile->id,
                    'esporte_id' => $jogo->pelada->esporte_id,
                ],
                [
                    'jogos' => $jogos,
                    'gols' => $gols,
                ]
            );
        }
    }
}
