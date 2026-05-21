<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\PeladaJogo;
use App\Models\Sorteio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SorteioController extends Controller
{
    public function show(PeladaJogo $jogo): View
    {
        $this->authorizeOwner($jogo);

        return view('organizador.sorteios.show', [
            'jogo' => $jogo->load('pelada'),
            'elegiveisCount' => $jogo->participantes()
                ->where('status', 'confirmado')
                ->whereHas('membro', fn ($query) => $query->where('status', 'ativo'))
                ->count(),
            'membrosPorUsuario' => $jogo->pelada->membros()->with('user')->get()->keyBy('user_id'),
            'sorteios' => $jogo->sorteios()->with('times.jogadores.user')->latest()->get(),
        ]);
    }

    public function sortear(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo);
        $quantidadeTimes = max(2, (int) $request->input('quantidade_times', 2));
        $participantes = $jogo->participantes()
            ->with(['user', 'membro'])
            ->where('status', 'confirmado')
            ->whereHas('membro', fn ($query) => $query->where('status', 'ativo'))
            ->inRandomOrder()
            ->get();

        if ($participantes->count() < $quantidadeTimes) {
            return back()->with('status', 'Participantes aceitos e confirmados insuficientes para sortear esses times.');
        }

        $sorteio = Sorteio::create([
            'pelada_jogo_id' => $jogo->id,
            'criado_por' => $request->user()->id,
            'tipo_sorteio' => $request->input('tipo_sorteio', 'simples'),
            'quantidade_times' => $quantidadeTimes,
            'status' => 'publicado',
            'realizado_em' => now(),
        ]);

        $times = collect(range(1, $quantidadeTimes))->map(fn ($numero) => $sorteio->times()->create([
            'nome' => 'Time '.$numero,
            'nome_time' => 'Time '.$numero,
            'ordem' => $numero,
        ]));

        foreach ($participantes->values() as $index => $participante) {
            $time = $times[$index % $quantidadeTimes];
            $time->jogadores()->create([
                'user_id' => $participante->user_id,
                'ordem' => intdiv($index, $quantidadeTimes) + 1,
            ]);
        }

        return back()->with('status', 'Times sorteados com participantes confirmados.');
    }

    private function authorizeOwner(PeladaJogo $jogo): void
    {
        $this->redirectIfNotPeladaOwner($jogo->pelada);
    }
}
