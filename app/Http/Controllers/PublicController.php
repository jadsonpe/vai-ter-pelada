<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Pelada;
use App\Models\Patrocinador;
use App\Models\User;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'banners' => Banner::where('ativo', true)->latest()->get(),
            'peladas' => Pelada::with(['esporte', 'organizador'])->where('ativa', true)->latest()->take(6)->get(),
            'patrocinadores' => Patrocinador::where('ativo', true)->get(),
        ]);
    }

    public function peladas(): View
    {
        return view('public.peladas', [
            'peladas' => Pelada::with(['esporte', 'organizador'])->where('ativa', true)->latest()->paginate(12),
        ]);
    }

    public function pelada(Pelada $pelada): View
    {
        return view('public.pelada', [
            'pelada' => $pelada->load(['esporte', 'organizador', 'jogos.participantes.user']),
        ]);
    }

    public function ranking(): View
    {
        return view('public.ranking', [
            'jogadores' => User::withCount(['participacoes' => fn ($query) => $query->where('status', 'confirmado')])
                ->where('role', 'jogador')
                ->orderByDesc('participacoes_count')
                ->take(30)
                ->get(),
        ]);
    }
}
