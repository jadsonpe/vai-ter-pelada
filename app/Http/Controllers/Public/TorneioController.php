<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Torneio;
use App\Services\TorneioService;
use Illuminate\View\View;

class TorneioController extends Controller
{
    public function __construct(private readonly TorneioService $service)
    {
    }

    public function show(Torneio $torneio): View
    {
        $torneio->load([
            'pelada.esporte',
            'times.jogadores.participante.user',
            'times.jogadores.participante.membro.user',
            'grupos.times',
            'jogos.timeA',
            'jogos.timeB',
            'jogos.vencedor',
        ]);

        return view('public.torneio', [
            'torneio' => $torneio,
            'classificacao' => $this->service->classificacao($torneio),
            'artilharia' => $this->service->artilharia($torneio),
            'disciplina' => $this->service->disciplina($torneio),
        ]);
    }
}
