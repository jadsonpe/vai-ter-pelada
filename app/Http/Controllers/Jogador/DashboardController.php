<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Models\PeladaJogo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('jogador.dashboard', [
            'membros' => $user->memberships()->with('pelada.esporte')->get(),
            'proximosJogos' => PeladaJogo::with('pelada')
                ->whereHas('pelada.membros', fn ($query) => $query->where('user_id', $user->id))
                ->where('data_hora', '>=', now())
                ->orderBy('data_hora')
                ->take(8)
                ->get(),
            'participacoes' => $user->participacoes()->with('jogo.pelada')->latest()->take(8)->get(),
        ]);
    }
}
