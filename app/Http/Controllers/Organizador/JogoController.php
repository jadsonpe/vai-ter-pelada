<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JogoController extends Controller
{
    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.jogos.index', [
            'pelada' => $pelada->load([
                'jogos' => fn ($query) => $query->orderByDesc('data_hora')->orderByDesc('id'),
                'jogos.participantes.user',
            ]),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);

        $data = $request->validate([
            'data_hora' => ['required', 'date'],
            'vagas_totais' => ['nullable', 'integer', 'min:2'],
            'vagas_diaristas' => ['nullable', 'integer', 'min:0'],
            'observacao' => ['nullable'],
        ]);

        $data['titulo'] = $this->proximoTituloRodada($pelada);
        $data['data_jogo'] = date('Y-m-d', strtotime($data['data_hora']));
        $data['horario'] = date('H:i:s', strtotime($data['data_hora']));
        $data['capacidade'] = $data['vagas_totais'] ?: $pelada->vagas_totais;

        $pelada->jogos()->create($data + ['status' => 'aberto']);

        return back()->with('status', 'Rodada criada.');
    }

    public function update(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);

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
        $this->authorizeOwner($jogo->pelada);

        $jogo->load('pelada.membros.user', 'participantes.user', 'participantes.membro.user');
        $usuariosJaListados = $jogo->participantes
            ->whereIn('status', ['confirmado', 'fila'])
            ->pluck('user_id');

        return view('organizador.jogos.participantes', [
            'jogo' => $jogo,
            'mensalistasDisponiveis' => $jogo->pelada->membros
                ->where('tipo', 'mensalista')
                ->where('status', 'ativo')
                ->whereNotIn('user_id', $usuariosJaListados)
                ->sortBy(fn (PeladaMembro $membro) => $membro->nomeExibicao()),
        ]);
    }

    public function confirmarMensalista(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);

        $data = $request->validate([
            'pelada_membro_id' => ['required', 'integer'],
        ]);

        $membro = $jogo->pelada->membros()
            ->where('id', $data['pelada_membro_id'])
            ->where('tipo', 'mensalista')
            ->where('status', 'ativo')
            ->firstOrFail();

        PeladaJogoParticipante::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'user_id' => $membro->user_id],
            [
                'pelada_membro_id' => $membro->id,
                'tipo' => 'mensalista',
                'tipo_no_jogo' => 'mensalista',
                'status' => 'confirmado',
                'ordem_chegada' => (int) $jogo->participantes()->max('ordem_chegada') + 1,
                'posicao_fila' => null,
                'confirmado_em' => now(),
                'cancelado_em' => null,
            ]
        );

        return back()->with('status', 'Mensalista confirmado na rodada.');
    }

    public function removerParticipante(PeladaJogo $jogo, PeladaJogoParticipante $participante): RedirectResponse
    {
        $this->authorizeOwner($jogo->pelada);
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
}
