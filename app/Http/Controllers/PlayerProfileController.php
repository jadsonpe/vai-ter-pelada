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
        $name = Str::limit($user->apelido ?: $user->name, 28, '');
        $sport = Str::limit($profile->esportePrincipal?->nome ?: 'Multiesporte', 18, '');
        $position = Str::limit($profile->posicao_favorita ?: 'Peladeiro', 18, '');
        $level = $this->rankingSocial($profile, $user);
        $score = number_format($profile->reputation_score + $user->points_total, 0, ',', '.');

        $image = imagecreatetruecolor(1200, 630);
        imageantialias($image, true);

        for ($x = 0; $x < 1200; $x++) {
            $ratio = $x / 1200;
            $color = imagecolorallocate(
                $image,
                (int) (16 * (1 - $ratio) + 2 * $ratio),
                (int) (185 * (1 - $ratio) + 6 * $ratio),
                (int) (129 * (1 - $ratio) + 23 * $ratio)
            );
            imageline($image, $x, 0, $x, 630, $color);
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocate($image, 203, 213, 225);
        $emerald = imagecolorallocate($image, 52, 211, 153);
        $dark = imagecolorallocate($image, 2, 6, 23);
        $panel = imagecolorallocatealpha($image, 2, 6, 23, 30);
        $circle = imagecolorallocatealpha($image, 16, 185, 129, 82);

        imagefilledrectangle($image, 72, 72, 1128, 558, $panel);
        imagerectangle($image, 72, 72, 1128, 558, $emerald);
        imagefilledellipse($image, 930, 315, 280, 280, $circle);
        imageellipse($image, 930, 315, 280, 280, $emerald);

        $boldFont = $this->fontPath(true);
        $regularFont = $this->fontPath();

        $this->drawText($image, 'VAI TER PELADA', 108, 145, 28, $emerald, $boldFont);
        $this->drawText($image, $name, 108, 255, 68, $white, $boldFont);
        $this->drawText($image, "{$sport} | {$position}", 108, 318, 32, $muted, $regularFont);
        imagefilledrectangle($image, 108, 368, 560, 450, $emerald);
        $this->drawText($image, $level, 136, 422, 32, $dark, $boldFont);
        $this->drawText($image, 'Reputacao + pontos: '.$score, 108, 505, 26, $muted, $regularFont);
        if (! $this->drawAvatar($image, $user, 930, 300, 220)) {
            imagefilledellipse($image, 930, 300, 220, 220, $emerald);
            $this->drawText($image, $user->initials(), 895, 330, 64, $dark, $boldFont);
        }

        $this->drawText($image, 'PERFIL PUBLICO', 800, 470, 24, $white, $boldFont);

        ob_start();
        imagepng($image, null, 6);
        $png = ob_get_clean();
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

    private function drawAvatar($image, User $user, int $centerX, int $centerY, int $size): bool
    {
        $avatar = $this->loadAvatarImage($user);

        if (! $avatar) {
            return false;
        }

        $sourceWidth = imagesx($avatar);
        $sourceHeight = imagesy($avatar);
        $crop = min($sourceWidth, $sourceHeight);
        $sourceX = (int) (($sourceWidth - $crop) / 2);
        $sourceY = (int) (($sourceHeight - $crop) / 2);
        $target = imagecreatetruecolor($size, $size);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $avatar, 0, 0, $sourceX, $sourceY, $size, $size, $crop, $crop);

        $mask = imagecreatetruecolor($size, $size);
        $transparent = imagecolorallocatealpha($mask, 0, 0, 0, 127);
        imagefill($mask, 0, 0, $transparent);
        $opaque = imagecolorallocate($mask, 255, 255, 255);
        imagefilledellipse($mask, (int) ($size / 2), (int) ($size / 2), $size, $size, $opaque);

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $alpha = imagecolorat($mask, $x, $y) & 0x7F;
                if ($alpha === 127) {
                    imagesetpixel($target, $x, $y, imagecolorallocatealpha($target, 0, 0, 0, 127));
                }
            }
        }

        imagecopy($image, $target, (int) ($centerX - $size / 2), (int) ($centerY - $size / 2), 0, 0, $size, $size);
        imagedestroy($avatar);
        imagedestroy($target);
        imagedestroy($mask);

        return true;
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
