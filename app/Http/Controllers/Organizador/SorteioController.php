<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\Sorteio;
use App\Services\SorteioPresencialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SorteioController extends Controller
{
    public function __construct(
        private readonly SorteioPresencialService $sorteioService,
    ) {}

    public function show(PeladaJogo $jogo): View
    {
        $this->authorizeOwner($jogo);

        return view('organizador.sorteios.show', [
            'jogo' => $jogo->load('pelada'),
            'confirmados' => $this->participantesConfirmados($jogo),
            'sorteios' => $jogo->sorteios()
                ->with(['times.jogadores.participante.membro', 'times.jogadores.user'])
                ->latest()
                ->get(),
        ]);
    }

    public function salvarPresencas(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo);

        $data = $request->validate([
            'presentes' => ['nullable', 'array'],
            'presentes.*' => ['integer'],
            'ordem' => ['nullable', 'array'],
            'ordem.*' => ['integer'],
        ]);

        $confirmados = $this->participantesConfirmados($jogo);
        $idsConfirmados = $confirmados->pluck('id')->all();
        $presentes = collect($data['presentes'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->intersect($idsConfirmados)
            ->values();

        $ordemLista = collect($data['ordem'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $presentes->contains($id))
            ->values();

        foreach ($confirmados as $participante) {
            $estaPresente = $presentes->contains($participante->id);
            $ordem = $estaPresente
                ? ($ordemLista->search($participante->id) !== false ? $ordemLista->search($participante->id) + 1 : null)
                : null;

            $participante->update([
                'presente_local' => $estaPresente,
                'ordem_presenca' => $ordem,
            ]);
        }

        return back()->with('status', 'Presenças no local atualizadas.');
    }

    public function adicionarAvulso(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo);

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
        ]);

        $proximaOrdem = (int) $jogo->participantes()->max('ordem_presenca') + 1;

        PeladaJogoParticipante::create([
            'pelada_jogo_id' => $jogo->id,
            'user_id' => null,
            'pelada_membro_id' => null,
            'nome_avulso' => $data['nome'],
            'tipo' => 'diarista',
            'tipo_no_jogo' => 'diarista',
            'status' => 'confirmado',
            'presente_local' => true,
            'ordem_presenca' => $proximaOrdem,
            'ordem_chegada' => $proximaOrdem,
            'confirmado_em' => now(),
        ]);

        return back()->with('status', 'Jogador avulso adicionado e marcado como presente.');
    }

    public function sortear(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo);

        $data = $request->validate([
            'jogadores_por_time' => ['required', 'integer', 'min:1', 'max:30'],
            'modo_ordenacao' => ['required', 'in:manual,prioridade'],
            'presentes' => ['required', 'array', 'min:1'],
            'presentes.*' => ['integer'],
            'ordem' => ['nullable', 'array'],
            'ordem.*' => ['integer'],
        ]);

        $this->salvarPresencasInterno($jogo, $data['presentes'], $data['ordem'] ?? []);

        $jogadoresPorTime = (int) $data['jogadores_por_time'];
        $usarOrdemManual = $request->input('modo_ordenacao') === 'manual';

        $confirmados = $this->participantesConfirmados($jogo);
        $presentesOrdenados = $this->sorteioService->ordenarPresentes($confirmados, $usarOrdemManual);

        if ($presentesOrdenados->isEmpty()) {
            throw ValidationException::withMessages([
                'presentes' => 'Marque pelo menos um jogador como presente no local.',
            ]);
        }

        if ($presentesOrdenados->count() < $jogadoresPorTime) {
            throw ValidationException::withMessages([
                'jogadores_por_time' => "É necessário pelo menos {$jogadoresPorTime} jogador(es) presentes para completar um time.",
            ]);
        }

        $resultado = $this->sorteioService->montarTimes($presentesOrdenados, $jogadoresPorTime);

        DB::transaction(function () use ($request, $jogo, $jogadoresPorTime, $usarOrdemManual, $resultado, $presentesOrdenados) {
            $sorteio = Sorteio::create([
                'pelada_jogo_id' => $jogo->id,
                'criado_por' => $request->user()->id,
                'tipo_sorteio' => 'presencial_quadra',
                'quantidade_times' => count($resultado['times']),
                'jogadores_por_time' => $jogadoresPorTime,
                'usar_ordem_manual' => $usarOrdemManual,
                'status' => 'publicado',
                'realizado_em' => now(),
            ]);

            foreach ($resultado['times'] as $timeData) {
                $time = $sorteio->times()->create([
                    'nome' => $timeData['nome'],
                    'nome_time' => $timeData['nome'],
                    'ordem' => $timeData['ordem'],
                ]);

                foreach ($timeData['participantes']->values() as $slot => $participante) {
                    $time->jogadores()->create([
                        'pelada_jogo_participante_id' => $participante->id,
                        'user_id' => $participante->user_id,
                        'ordem' => $slot + 1,
                    ]);
                }
            }
        });

        $vagasIniciais = $jogadoresPorTime * 2;
        $timesExtras = max(0, count($resultado['times']) - 2);

        return back()->with(
            'status',
            "Sorteio realizado: {$presentesOrdenados->count()} presentes, {$vagasIniciais} vagas no 1º jogo (A x B)".($timesExtras > 0 ? " e mais {$timesExtras} time(s) na fila." : '.')
        );
    }

    /** @return Collection<int, PeladaJogoParticipante> */
    private function participantesConfirmados(PeladaJogo $jogo): Collection
    {
        return $jogo->participantes()
            ->with(['user', 'membro'])
            ->where('status', 'confirmado')
            ->orderByRaw('CASE WHEN ordem_presenca IS NULL THEN 1 ELSE 0 END')
            ->orderBy('ordem_presenca')
            ->orderBy('ordem_chegada')
            ->orderBy('id')
            ->get();
    }

    /** @param  array<int>  $presentesIds */
    private function salvarPresencasInterno(PeladaJogo $jogo, array $presentesIds, array $ordemIds): void
    {
        $confirmados = $this->participantesConfirmados($jogo);
        $presentes = collect($presentesIds)->map(fn ($id) => (int) $id);
        $ordemLista = collect($ordemIds)->map(fn ($id) => (int) $id);

        foreach ($confirmados as $participante) {
            $estaPresente = $presentes->contains($participante->id);
            $posicao = $ordemLista->search($participante->id);

            $participante->update([
                'presente_local' => $estaPresente,
                'ordem_presenca' => $estaPresente && $posicao !== false ? $posicao + 1 : null,
            ]);
        }
    }

    private function authorizeOwner(PeladaJogo $jogo): void
    {
        $this->redirectIfNotPeladaOwner($jogo->pelada);
    }
}
