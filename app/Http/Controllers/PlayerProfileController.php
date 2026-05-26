<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PlayerProfileController extends Controller
{
    public function show(PlayerProfile $profile): View
    {
        abort_unless($profile->publico, 404);

        $profile->load([
            'user.esportePerfis.esporte',
            'user.badges',
            'esportePrincipal',
            'socialLinks',
            'stats.esporte',
            'votes',
            'achievements',
            'rankings',
        ]);

        $user = $profile->user;
        $peladas = $user->memberships()
            ->with('pelada.esporte')
            ->where('status', 'ativo')
            ->latest()
            ->take(6)
            ->get();

        $jogosConfirmados = $user->participacoes()->where('status', 'confirmado')->count();
        $stats = $this->stats($profile, $jogosConfirmados);
        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();
        $isFollowing = auth()->check() && auth()->id() !== $user->id
            ? auth()->user()->isFollowing($user)
            : false;

        return view('public.peladeiro', [
            'profile' => $profile,
            'jogador' => $user,
            'peladas' => $peladas,
            'stats' => $stats,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'isFollowing' => $isFollowing,
            'socialLinks' => $profile->socialLinks->keyBy('platform'),
            'rankingSocial' => $this->rankingSocial($profile, $user),
            'voteLabels' => [
                'craque' => 'Craque da rodada',
                'garcom' => 'Garcom',
                'muralha' => 'Muralha',
                'fair_play' => 'Fair play',
                'perna_de_pau' => 'Perna de pau',
            ],
            'whatsappShareUrl' => 'https://wa.me/?text='.rawurlencode(
                'Olha meu perfil no Vai Ter Pelada: '.$profile->shareUrl()
            ),
        ]);
    }

    public function legacy(User $user): RedirectResponse
    {
        return redirect()->route('peladeiros.show', $user->publicProfile());
    }

    public function follow(Request $request, PlayerProfile $profile): RedirectResponse
    {
        abort_unless($profile->publico, 404);

        $target = $profile->user;
        abort_if($request->user()->is($target), 403);

        $request->user()->following()->syncWithoutDetaching([$target->id]);

        return back()->with('status', 'Voce agora segue este peladeiro.');
    }

    public function unfollow(Request $request, PlayerProfile $profile): RedirectResponse
    {
        abort_unless($profile->publico, 404);

        $request->user()->following()->detach($profile->user_id);

        return back()->with('status', 'Voce deixou de seguir este peladeiro.');
    }

    public function followers(PlayerProfile $profile): View
    {
        abort_unless($profile->publico, 404);

        return $this->followList($profile, 'seguidores');
    }

    public function following(PlayerProfile $profile): View
    {
        abort_unless($profile->publico, 404);

        return $this->followList($profile, 'seguindo');
    }

    public function card(PlayerProfile $profile): Response
    {
        abort_unless($profile->publico, 404);

        $profile->load(['user', 'esportePrincipal']);
        $user = $profile->user;
        $name = e($user->apelido ?: $user->name);
        $sport = e($profile->esportePrincipal?->nome ?: 'Multiesporte');
        $position = e($profile->posicao_favorita ?: 'Peladeiro');
        $level = e($this->rankingSocial($profile, $user));
        $score = number_format($profile->reputation_score + $user->points_total, 0, ',', '.');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#10b981"/>
      <stop offset=".45" stop-color="#0f172a"/>
      <stop offset="1" stop-color="#020617"/>
    </linearGradient>
    <radialGradient id="shine" cx=".78" cy=".18" r=".6">
      <stop offset="0" stop-color="#34d399" stop-opacity=".48"/>
      <stop offset="1" stop-color="#34d399" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <rect width="1200" height="630" fill="url(#shine)"/>
  <rect x="72" y="72" width="1056" height="486" rx="36" fill="#020617" opacity=".62" stroke="#34d399" stroke-opacity=".45"/>
  <text x="108" y="145" font-family="Arial, sans-serif" font-size="30" font-weight="800" fill="#6ee7b7" letter-spacing="6">VAI TER PELADA</text>
  <text x="108" y="250" font-family="Arial, sans-serif" font-size="78" font-weight="900" fill="#ffffff">{$name}</text>
  <text x="108" y="315" font-family="Arial, sans-serif" font-size="34" font-weight="700" fill="#cbd5e1">{$sport} • {$position}</text>
  <rect x="108" y="368" width="420" height="82" rx="20" fill="#10b981"/>
  <text x="136" y="420" font-family="Arial, sans-serif" font-size="34" font-weight="900" fill="#020617">{$level}</text>
  <text x="108" y="505" font-family="Arial, sans-serif" font-size="28" font-weight="700" fill="#e2e8f0">Reputacao + pontos: {$score}</text>
  <circle cx="928" cy="315" r="128" fill="#10b981" opacity=".18" stroke="#6ee7b7" stroke-width="8"/>
  <text x="928" y="300" text-anchor="middle" font-family="Arial, sans-serif" font-size="44" font-weight="900" fill="#ffffff">PERFIL</text>
  <text x="928" y="356" text-anchor="middle" font-family="Arial, sans-serif" font-size="44" font-weight="900" fill="#6ee7b7">PUBLICO</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function followList(PlayerProfile $profile, string $mode): View
    {
        $profile->load('user');
        $query = $mode === 'seguidores'
            ? $profile->user->followers()
            : $profile->user->following();

        $users = $query
            ->with(['playerProfile.esportePrincipal'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(24);

        return view('public.peladeiro-follow-list', [
            'profile' => $profile,
            'jogador' => $profile->user,
            'users' => $users,
            'mode' => $mode,
        ]);
    }

    private function stats(PlayerProfile $profile, int $jogosConfirmados): array
    {
        $stat = $profile->stats->firstWhere('esporte_id', $profile->esporte_principal_id)
            ?: $profile->stats->first();

        return [
            'jogos' => $stat?->jogos ?: $jogosConfirmados,
            'vitorias' => $stat?->vitorias ?: 0,
            'gols' => $stat?->gols ?: 0,
            'assistencias' => $stat?->assistencias ?: 0,
            'mvps' => $stat?->mvps ?: $profile->votes->where('type', 'craque')->count(),
            'aproveitamento' => $stat?->aproveitamento ?: 0,
            'sequencia_vitorias' => $stat?->sequencia_vitorias ?: 0,
            'media' => $profile->user->rating_average,
            'craque_votes' => $profile->votes->where('type', 'craque')->count(),
            'fair_play_votes' => $profile->votes->where('type', 'fair_play')->count(),
            'destaques' => $profile->votes->whereIn('type', ['craque', 'garcom', 'muralha', 'fair_play'])->count(),
        ];
    }

    private function rankingSocial(PlayerProfile $profile, User $user): string
    {
        $score = $profile->reputation_score + $user->points_total + ($user->rating_count * 10);

        return match (true) {
            $score >= 1000 => 'Dono da Bola',
            $score >= 600 => 'Rei da Quadra',
            $score >= 300 => 'Craque do Baba',
            $score >= 120 => 'Reserva de Luxo',
            default => 'Perna de Pau',
        };
    }
}
