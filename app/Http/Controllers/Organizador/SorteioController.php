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
            'sorteios' => $jogo->sorteios()->with('times.jogadores.user')->latest()->get(),
        ]);
    }

    public function sortear(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo);
        $quantidadeTimes = max(2, (int) $request->input('quantidade_times', 2));
        $participantes = $jogo->participantes()
            ->with('user')
            ->where('status', 'confirmado')
            ->inRandomOrder()
            ->get();

        abort_if($participantes->count() < $quantidadeTimes, 422, 'Participantes confirmados insuficientes.');

        $sorteio = Sorteio::create([
            'pelada_jogo_id' => $jogo->id,
            'criado_por' => $request->user()->id,
            'quantidade_times' => $quantidadeTimes,
            'realizado_em' => now(),
        ]);

        $times = collect(range(1, $quantidadeTimes))->map(fn ($numero) => $sorteio->times()->create([
            'nome' => 'Time '.$numero,
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
        abort_unless(auth()->user()->isAdmin() || $jogo->pelada->organizador_id === auth()->id(), 403);
    }
}
