<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\PlayerVote;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipanteEstatistica;
use App\Models\Notificacao;
use App\Models\Report;
use App\Models\TorneioCartao;
use App\Models\TorneioGol;
use App\Models\TorneioJogo;
use App\Models\TorneioParticipante;
use App\Models\TorneioTimeJogador;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
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
        $avaliacoesPorRodada = $this->avaliacoesPorRodada($user);

        $peladaPerformance = $this->peladaPerformance($profile);
        $torneioPerformance = $this->torneioPerformance($user);
        $stats = $this->stats($peladaPerformance, $torneioPerformance);
        $mediaAvaliacoesRecentes = round($avaliacoesPorRodada->avg('media') ?? 0, 2);
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
        $posts = $profile->posts()
            ->publicado()
            ->with(['user', 'profile'])
            ->withCount('likes')
            ->latest('publicado_em')
            ->latest()
            ->get();
        $likedPostIds = auth()->check() && $posts->isNotEmpty()
            ? auth()->user()->likedPosts()
                ->whereIn('player_posts.id', $posts->pluck('id'))
                ->pluck('player_posts.id')
                ->all()
            : [];

        return view('public.peladeiro', [
            'profile' => $profile,
            'jogador' => $user,
            'peladas' => $peladas,
            'avaliacoesPorRodada' => $avaliacoesPorRodada,
            'stats' => $stats,
            'peladaPerformance' => $peladaPerformance,
            'torneioPerformance' => $torneioPerformance,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'followersPreview' => $followersPreview,
            'followingPreview' => $followingPreview,
            'isFollowing' => $isFollowing,
            'socialLinks' => $profile->socialLinks->keyBy('platform'),
            'rankingSocial' => $this->rankingSocialDaUltimaRodada($profile),
            'reputation' => [
                'nivel' => PlayerProfile::levelForScore((int) $profile->reputation_score),
                'score' => (int) $profile->reputation_score,
                'pontos' => $user->points_total,
                'avaliacoes_media' => $mediaAvaliacoesRecentes,
                'avaliacoes_total' => $avaliacoesPorRodada->sum('total'),
            ],
            'voteLabels' => $this->voteLabels(),
            'voteCounts' => $voteCounts,
            'reportReasons' => Report::reasonsFor('jogador'),
            'posts' => $posts,
            'likedPostIds' => $likedPostIds,
            'postReportReasons' => Report::reasonsFor('publicacao'),
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

        $follower = $request->user();
        $changes = $follower->following()->syncWithoutDetaching([$target->id]);

        if (! empty($changes['attached'])) {
            $followerProfile = $follower->publicProfile();
            $followerName = $follower->apelido ?: $follower->name ?: 'Um peladeiro';

            Notificacao::create([
                'user_id' => $target->id,
                'titulo' => 'Novo seguidor',
                'mensagem' => "{$followerName} seguiu você.",
                'link' => route('peladeiros.show', $followerProfile),
            ]);

            Cache::forget("users.{$target->id}.notificacoes_nao_lidas");
        }

        return back()->with('status', 'Você agora segue este peladeiro.');
    }

    public function unfollow(Request $request, PlayerProfile $profile): RedirectResponse
    {
        abort_unless($profile->publico, 404);

        $request->user()->following()->detach($profile->user_id);

        return back()->with('status', 'Você deixou de seguir este peladeiro.');
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

    private function stats(array $peladaPerformance, array $torneioPerformance): array
    {
        return [
            'jogos' => $peladaPerformance['jogos'] + $torneioPerformance['jogos'],
            'gols' => $peladaPerformance['gols'] + $torneioPerformance['gols'],
            'cartoes' => $peladaPerformance['cartoes'] + $torneioPerformance['cartoes'],
        ];
    }

    private function peladaPerformance(PlayerProfile $profile): array
    {
        $cartaoStats = PeladaJogoParticipanteEstatistica::query()
            ->where('user_id', $profile->user_id);
        $gols = (int) PeladaJogoParticipanteEstatistica::query()
            ->where('user_id', $profile->user_id)
            ->sum('gols');
        $rodadas = $profile->user->participacoes()
            ->where('status', 'confirmado')
            ->where('presente_local', true)
            ->whereHas('jogo', fn ($query) => $query->where('status', 'finalizado'))
            ->distinct('pelada_jogo_id')
            ->count('pelada_jogo_id');

        return [
            'jogos' => $rodadas,
            'gols' => $gols,
            'cartoes' => (clone $cartaoStats)->sum('cartoes_amarelos')
                + (clone $cartaoStats)->sum('cartoes_vermelhos')
                + (clone $cartaoStats)->sum('cartoes_azuis'),
            'cartoes_amarelos' => (clone $cartaoStats)->sum('cartoes_amarelos'),
            'cartoes_vermelhos' => (clone $cartaoStats)->sum('cartoes_vermelhos'),
            'cartoes_azuis' => (clone $cartaoStats)->sum('cartoes_azuis'),
        ];
    }

    private function torneioPerformance(User $user): array
    {
        $participanteIds = TorneioParticipante::query()
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->pluck('id');

        if ($participanteIds->isEmpty()) {
            return [
                'tem_dados' => false,
                'torneios' => 0,
                'jogos' => 0,
                'gols' => 0,
                'cartoes' => 0,
                'cartoes_amarelos' => 0,
                'cartoes_vermelhos' => 0,
                'cartoes_azuis' => 0,
            ];
        }

        $timeIds = TorneioTimeJogador::query()
            ->whereIn('torneio_participante_id', $participanteIds)
            ->pluck('torneio_time_id');

        $cartoes = TorneioCartao::query()
            ->whereIn('torneio_participante_id', $participanteIds);

        return [
            'tem_dados' => true,
            'torneios' => TorneioParticipante::query()
                ->whereIn('id', $participanteIds)
                ->distinct('torneio_id')
                ->count('torneio_id'),
            'jogos' => $timeIds->isEmpty()
                ? 0
                : TorneioJogo::query()
                    ->where('status', 'finalizado')
                    ->where(function ($query) use ($timeIds) {
                        $query->whereIn('time_a_id', $timeIds)
                            ->orWhereIn('time_b_id', $timeIds);
                    })
                    ->distinct('id')
                    ->count('id'),
            'gols' => (int) TorneioGol::query()
                ->whereIn('torneio_participante_id', $participanteIds)
                ->sum('quantidade'),
            'cartoes_amarelos' => (clone $cartoes)->where('tipo', 'amarelo')->sum('quantidade'),
            'cartoes_vermelhos' => (clone $cartoes)->where('tipo', 'vermelho')->sum('quantidade'),
            'cartoes_azuis' => (clone $cartoes)->where('tipo', 'azul')->sum('quantidade'),
            'cartoes' => (clone $cartoes)->sum('quantidade'),
        ];
    }

    private function rankingSocialDaUltimaRodada(PlayerProfile $profile): string
    {
        $ultimaRodada = PeladaJogo::query()
            ->where('status', 'finalizado')
            ->whereHas('participantes', fn ($query) => $query
                ->where('user_id', $profile->user_id)
                ->where('status', 'confirmado')
                ->where('presente_local', true))
            ->orderByDesc('finalizada_em')
            ->orderByDesc('data_hora')
            ->first();

        if (! $ultimaRodada) {
            return 'Peladeiro';
        }

        $voteCounts = PlayerVote::query()
            ->where('player_profile_id', $profile->id)
            ->where('pelada_jogo_id', $ultimaRodada->id)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return $this->rankingSocial($voteCounts);
    }

    private function rankingSocial($voteCounts): string
    {
        $winner = collect($this->voteLabels())
            ->map(function (string $label, string $type) use ($voteCounts) {
                return [
                    'label' => $label,
                    'count' => (int) ($voteCounts[$type] ?? 0),
                ];
            })
            ->filter(fn (array $vote) => $vote['count'] > 0)
            ->sortByDesc('count')
            ->first();

        return $winner['label'] ?? 'Peladeiro';
    }

    private function avaliacoesPorRodada(User $user)
    {
        return $user->avaliacoesRecebidas()
            ->with(['jogo.pelada'])
            ->latest()
            ->take(200)
            ->get()
            ->filter(fn ($avaliacao) => $avaliacao->jogo?->status === 'finalizado')
            ->groupBy('pelada_jogo_id')
            ->map(function ($avaliacoes) {
                $primeira = $avaliacoes->first();

                return [
                    'jogo' => $primeira->jogo,
                    'media' => round($avaliacoes->avg('estrelas'), 2),
                    'total' => $avaliacoes->count(),
                    'ultima_avaliacao_em' => $avaliacoes->max('created_at'),
                ];
            })
            ->sortByDesc('ultima_avaliacao_em')
            ->take(5)
            ->values();
    }

    private function voteLabels(): array
    {
        return [
            'craque' => 'Craque da rodada',
            'garcom' => 'Garçom',
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
