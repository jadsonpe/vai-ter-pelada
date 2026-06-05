<?php

namespace App\Http\Controllers;

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
            'term' => $term,
            'normalized' => $normalized,
        ]);
    }
}
