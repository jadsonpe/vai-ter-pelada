<?php

namespace App\Http\Controllers;

use App\Models\PeladaJogoParticipante;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JogadorSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->query('busca', ''));
        $normalized = ltrim(str($term)->lower()->toString(), '@');
        $players = null;
        $featuredPlayers = $this->featuredPlayers();

        if (mb_strlen($normalized) >= 2) {
            $like = '%'.$normalized.'%';

            $players = User::query()
                ->select(['id', 'name', 'apelido', 'username', 'avatar_url', 'avatar_path', 'active', 'status'])
                ->with(['playerProfile:id,user_id,slug,publico'])
                ->where('active', true)
                ->where(function ($query): void {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', 'bloqueado');
                })
                ->where(function ($query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('apelido', 'like', $like)
                        ->orWhere('username', 'like', $like);
                })
                ->orderByRaw('case when username = ? then 0 when username like ? then 1 else 2 end', [$normalized, $normalized.'%'])
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString();
        }

        return view('jogadores.index', [
            'players' => $players,
            'featuredPlayers' => $featuredPlayers,
            'term' => $term,
            'normalized' => $normalized,
        ]);
    }

    private function featuredPlayers()
    {
        $latestParticipation = PeladaJogoParticipante::query()
            ->select('pelada_jogos.data_hora')
            ->join('pelada_jogos', 'pelada_jogos.id', '=', 'pelada_jogo_participantes.pelada_jogo_id')
            ->whereColumn('pelada_jogo_participantes.user_id', 'users.id')
            ->whereIn('pelada_jogo_participantes.status', ['confirmado', 'compareceu'])
            ->where('pelada_jogos.data_hora', '<=', now())
            ->where('pelada_jogos.data_hora', '>=', now()->subDays(90))
            ->latest('pelada_jogos.data_hora')
            ->limit(1);

        return User::query()
            ->select(['id', 'name', 'apelido', 'username', 'avatar_url', 'avatar_path', 'active', 'status'])
            ->with(['playerProfile:id,user_id,slug,publico'])
            ->withCount([
                'participacoes as participacoes_recentes_count' => fn ($query) => $query
                    ->whereIn('status', ['confirmado', 'compareceu'])
                    ->whereHas('jogo', fn ($query) => $query
                        ->where('data_hora', '<=', now())
                        ->where('data_hora', '>=', now()->subDays(90))),
            ])
            ->where('active', true)
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'bloqueado');
            })
            ->whereHas('participacoes', fn ($query) => $query
                ->whereIn('status', ['confirmado', 'compareceu'])
                ->whereHas('jogo', fn ($query) => $query
                    ->where('data_hora', '<=', now())
                    ->where('data_hora', '>=', now()->subDays(90))))
            ->orderByDesc($latestParticipation)
            ->take(8)
            ->get();
    }
}
