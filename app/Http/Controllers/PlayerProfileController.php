<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $followersPreview = $user->followers()
            ->with(['playerProfile.esportePrincipal'])
            ->whereHas('playerProfile', fn ($query) => $query->where('publico', true))
            ->orderByPivot('created_at', 'desc')
            ->take(6)
            ->get();
        $followingPreview = $user->following()
            ->with(['playerProfile.esportePrincipal'])
            ->whereHas('playerProfile', fn ($query) => $query->where('publico', true))
            ->orderByPivot('created_at', 'desc')
            ->take(6)
            ->get();
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
            'followersPreview' => $followersPreview,
            'followingPreview' => $followingPreview,
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

        $profile->load(['user']);
        $user = $profile->user;
        $image = imagecreatetruecolor(800, 800);
        imageantialias($image, true);
        imagealphablending($image, true);

        $avatar = $this->loadAvatarImage($user);

        if ($avatar) {
            $this->drawSquareImage($image, $avatar, 800);
        } else {
            $this->drawInitialsImage($image, $user);
        }

        ob_start();
        imagepng($image, null, 6);
        $png = ob_get_clean();
        if ($avatar) {
            imagedestroy($avatar);
        }
        imagedestroy($image);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function legacyCard(PlayerProfile $profile): RedirectResponse
    {
        return redirect()->route('peladeiros.card', $profile, 301);
    }

    private function followList(PlayerProfile $profile, string $mode): View
    {
        $profile->load('user');
        $query = $mode === 'seguidores'
            ? $profile->user->followers()
            : $profile->user->following();

        $users = $query
            ->with(['playerProfile.esportePrincipal'])
            ->whereHas('playerProfile', fn ($query) => $query->where('publico', true))
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
        $votesCount = $profile->votes()->count();
        $ratingCount = $user->rating_count;
        $score = $profile->reputation_score + $user->points_total + ($ratingCount * 10);

        if ($score <= 0 && $ratingCount === 0 && $votesCount === 0) {
            return 'Novato';
        }

        return match (true) {
            $score >= 1000 => 'Dono da Bola',
            $score >= 600 => 'Rei da Quadra',
            $score >= 300 => 'Craque do Baba',
            $score >= 120 => 'Reserva de Luxo',
            default => 'Perna de Pau',
        };
    }

    private function drawText($image, string $text, int $x, int $y, int $size, int $color, ?string $font = null): void
    {
        if ($font) {
            imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
            return;
        }

        imagestring($image, 5, $x, $y - 18, $text, $color);
    }

    private function drawSquareImage($image, $avatar, int $size): void
    {
        $sourceWidth = imagesx($avatar);
        $sourceHeight = imagesy($avatar);
        $crop = min($sourceWidth, $sourceHeight);
        $sourceX = (int) (($sourceWidth - $crop) / 2);
        $sourceY = (int) (($sourceHeight - $crop) / 2);

        imagecopyresampled($image, $avatar, 0, 0, $sourceX, $sourceY, $size, $size, $crop, $crop);
    }

    private function drawInitialsImage($image, User $user): void
    {
        $background = imagecolorallocate($image, 16, 185, 129);
        $text = imagecolorallocate($image, 2, 6, 23);
        imagefilledrectangle($image, 0, 0, 800, 800, $background);
        $this->drawText($image, $user->initials(), 315, 455, 140, $text, $this->fontPath(true));
    }

    private function loadAvatarImage(User $user)
    {
        $contents = null;

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            $contents = Storage::disk('public')->get($user->avatar_path);
        } elseif ($user->avatar_url && str_starts_with($user->avatar_url, 'http')) {
            $contents = @file_get_contents($user->avatar_url);
        }

        if (! $contents) {
            return null;
        }

        return @imagecreatefromstring($contents) ?: null;
    }

    private function fontPath(bool $bold = false): ?string
    {
        $paths = $bold
            ? [
                'C:\Windows\Fonts\arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                'C:\Windows\Fonts\arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
