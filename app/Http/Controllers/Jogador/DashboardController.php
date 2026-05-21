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
        $membros = $user->memberships()->with('pelada.esporte')->get();
        $participacoes = $user->participacoes()->with('jogo.pelada')->latest()->take(8)->get();
        $notificacoes = $user->notificacoes()->latest()->take(8)->get();
        $user->notificacoes()->whereNull('lida_em')->update(['lida_em' => now()]);
        $proximosJogos = PeladaJogo::with(['pelada.esporte', 'participantes' => fn ($query) => $query->where('user_id', $user->id)])
            ->withCount([
                'participantes as confirmados_count' => fn ($query) => $query->where('status', 'confirmado'),
                'participantes as fila_count' => fn ($query) => $query->where('status', 'fila'),
            ])
            ->whereHas('pelada.membros', fn ($query) => $query->where('user_id', $user->id))
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->take(8)
            ->get();

        return view('dashboard', [
            'membros' => $membros,
            'proximosJogos' => $proximosJogos,
            'participacoes' => $participacoes,
            'mensalistasCount' => $membros->where('tipo', 'mensalista')->where('status', 'ativo')->count(),
            'diaristasCount' => $membros->where('tipo', 'diarista')->where('status', 'ativo')->count(),
            'confirmacoesCount' => $participacoes->where('status', 'confirmado')->count(),
            'notificacoes' => $notificacoes,
        ]);
    }
}
