<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\Patrocinador;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function peladas(Request $request): View
    {
        $peladasQuery = Pelada::with(['esporte', 'organizador'])
            ->where('ativa', true)
            ->where('status', 'ativa');

        if ($request->filled('cidade')) {
            $peladasQuery->where('cidade', $request->cidade);
        }

        if ($request->filled('bairro')) {
            $peladasQuery->where('bairro', $request->bairro);
        }

        if ($request->filled('esporte_id')) {
            $peladasQuery->where('esporte_id', $request->esporte_id);
        }

        $rodadasQuery = PeladaJogo::with(['pelada.esporte', 'pelada.organizador'])
            ->withCount([
                'participantes as confirmados_count' => fn ($query) => $query->where('status', 'confirmado'),
            ])
            ->whereIn('status', ['aberto', 'fechado'])
            ->whereBetween('data_hora', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->whereHas('pelada', function ($query) use ($request) {
                $query->where('ativa', true)->where('status', 'ativa');

                if ($request->filled('cidade')) {
                    $query->where('cidade', $request->cidade);
                }

                if ($request->filled('bairro')) {
                    $query->where('bairro', $request->bairro);
                }

                if ($request->filled('esporte_id')) {
                    $query->where('esporte_id', $request->esporte_id);
                }
            });

        return view('public.peladas', [
            'peladas' => $peladasQuery->latest()->paginate(12)->withQueryString(),
            'rodadas' => $rodadasQuery->orderBy('data_hora')->get(),
            'esportes' => Esporte::where('ativo', true)->orderBy('nome')->get(),
            'cidades' => Pelada::where('ativa', true)->whereNotNull('cidade')->distinct()->orderBy('cidade')->pluck('cidade'),
            'bairros' => Pelada::where('ativa', true)->whereNotNull('bairro')->distinct()->orderBy('bairro')->pluck('bairro'),
            'filtros' => $request->only(['cidade', 'bairro', 'esporte_id']),
        ]);
    }

    public function pelada(Pelada $pelada): View
    {
        $user = auth()->user();

        return view('public.pelada', [
            'pelada' => $pelada->load(['esporte', 'organizador', 'jogos.participantes.user']),
            'membro' => $user ? $pelada->membros()->where('user_id', $user->id)->first() : null,
            'isOwner' => $user && $pelada->organizador_id === $user->id,
            'solicitacaoPendente' => $user
                ? $pelada->solicitacoes()
                    ->where('user_id', $user->id)
                    ->where('status', 'pendente')
                    ->latest()
                    ->first()
                : null,
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
