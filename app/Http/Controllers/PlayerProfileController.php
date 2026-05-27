<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\PlayerVote;
use App\Models\PeladaJogo;
use App\Models\Report;
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
            'achievements',
            'rankings',
        ]);

        $user = $profile->user;
        $voteCounts = PlayerVote::query()
            ->where('player_profile_id', $profile->id)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $peladas = $user->memberships()
            ->with('pelada.esporte')
            ->where('status', 'ativo')
            ->latest()
            ->take(6)
            ->get();

        $jogosConfirmados = $user->participacoes()->where('status', 'confirmado')->count();
        $stats = $this->stats($profile, $jogosConfirmados, $voteCounts);
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
            'voteLabels' => $this->voteLabels(),
            'voteCounts' => $voteCounts,
            'reportReasons' => Report::reasonsFor('jogador'),
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

    private function stats(PlayerProfile $profile, int $jogosConfirmados, $voteCounts): array
    {
        $stat = $profile->stats->firstWhere('esporte_id', $profile->esporte_principal_id)
            ?: $profile->stats->first();

        return [
            'jogos' => $stat?->jogos ?: $jogosConfirmados,
            'vitorias' => $stat?->vitorias ?: 0,
            'gols' => $stat?->gols ?: 0,
            'assistencias' => $stat?->assistencias ?: 0,
            'mvps' => $stat?->mvps ?: (int) ($voteCounts['craque'] ?? 0),
            'aproveitamento' => $stat?->aproveitamento ?: 0,
            'sequencia_vitorias' => $stat?->sequencia_vitorias ?: 0,
            'media' => $profile->user->rating_average,
            'craque_votes' => (int) ($voteCounts['craque'] ?? 0),
            'fair_play_votes' => (int) ($voteCounts['fair_play'] ?? 0),
            'destaques' => collect($this->voteLabels())->keys()->sum(fn ($type) => (int) ($voteCounts[$type] ?? 0)),
        ];
    }

    private function rankingSocial(PlayerProfile $profile, User $user): string
    {
        $lastJogo = PeladaJogo::query()
            ->where('data_hora', '<=', now())
            ->whereIn('status', ['realizado', 'finalizado'])
            ->whereHas('participantes', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('presente_local', true))
            ->orderByDesc('data_hora')
            ->first();

        if (! $lastJogo) {
            return 'Peladeiro';
        }

        $participantProfileIds = $lastJogo->participantes()
            ->where('presente_local', true)
            ->whereNotNull('user_id')
            ->with('user.playerProfile')
            ->get()
            ->map(fn ($participante) => $participante->user?->playerProfile?->id)
            ->filter()
            ->values();

        if ($participantProfileIds->isEmpty()) {
            return 'Peladeiro';
        }

        $votesByType = PlayerVote::query()
            ->where('pelada_jogo_id', $lastJogo->id)
            ->whereIn('player_profile_id', $participantProfileIds)
            ->selectRaw('type, player_profile_id, COUNT(*) as total')
            ->groupBy('type', 'player_profile_id')
            ->get()
            ->groupBy('type');

        $winner = collect($this->voteLabels())
            ->map(function (string $label, string $type) use ($votesByType, $profile) {
                $counts = $votesByType->get($type, collect())
                    ->pluck('total', 'player_profile_id');

                $max = (int) $counts->max();
                $playerTotal = (int) ($counts[$profile->id] ?? 0);

                return [
                    'label' => $label,
                    'count' => $playerTotal,
                    'won' => $playerTotal > 0 && $playerTotal === $max,
                ];
            })
            ->filter(fn (array $vote) => $vote['won'])
            ->sortByDesc('count')
            ->first();

        return $winner['label'] ?? 'Peladeiro';
    }

    private function voteLabels(): array
    {
        return [
            'craque' => 'Craque da rodada',
            'garcom' => 'Garcom',
            'muralha' => 'Muralha',
            'fair_play' => 'Fair play',
            'carcara' => 'Carcara',
            'fominha' => 'Fominha',
            'maestro' => 'Maestro',
            'xerife' => 'Xerife',
        ];
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
