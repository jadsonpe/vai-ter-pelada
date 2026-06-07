<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PlayerPostController;
use App\Models\AvaliacaoPartida;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaSolicitacao;
use App\Models\PlayerPost;
use App\Models\PlayerVote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $peladasOrganizadas = Pelada::query()
            ->with(['esporte'])
            ->withCount(['membros', 'jogos'])
            ->where(function ($query) use ($user) {
                $query->where('organizador_id', $user->id)
                    ->orWhereHas('membros', function ($membros) use ($user) {
                        $membros->where('user_id', $user->id)
                            ->where('status', 'ativo')
                            ->whereIn('papel', ['organizador', 'diretor']);
                    });
            })
            ->latest()
            ->get();

        $abasPermitidas = ['resumo', 'peladas', 'avaliacoes', 'mensagens'];
        if ($peladasOrganizadas->isNotEmpty()) {
            $abasPermitidas[] = 'organizacao';
        }

        $aba = in_array($request->query('aba'), $abasPermitidas, true)
            ? $request->query('aba')
            : 'resumo';

        $membros = $user->memberships()
            ->with([
                'pelada.esporte',
                'pelada.jogos' => fn ($query) => $query
                    ->whereIn('status', ['aberto', 'fechado'])
                    ->where('data_hora', '>=', now()->startOfDay())
                    ->orderBy('data_hora'),
                'pelada.jogos.participantes' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->get();

        $participacoes = $user->participacoes()->with('jogo.pelada')->latest()->take(5)->get();
        $notificacoes = $user->notificacoes()->latest()->take(5)->get();
        $notificacoesNaoLidas = $user->notificacoes()->whereNull('lida_em')->count();
        $followingIds = $user->following()->pluck('users.id');
        $feedPosts = PlayerPost::query()
            ->publicado()
            ->with(['user.playerProfile'])
            ->withCount('likes')
            ->whereIn('user_id', $followingIds)
            ->latest('publicado_em')
            ->latest()
            ->take(20)
            ->get();
        $likedFeedPostIds = $feedPosts->isNotEmpty()
            ? $user->likedPosts()
                ->whereIn('player_posts.id', $feedPosts->pluck('id'))
                ->pluck('player_posts.id')
                ->all()
            : [];

        if ($aba === 'mensagens' || $aba === 'resumo') {
            $user->notificacoes()->whereNull('lida_em')->update(['lida_em' => now()]);
        }
        
        $proximosJogos = PeladaJogo::with(['pelada.esporte', 'participantes' => fn ($query) => $query->where('user_id', $user->id)])
            ->withCount([
                'participantes as confirmados_count' => fn ($query) => $query->where('status', 'confirmado'),
                'participantes as fila_count' => fn ($query) => $query->where('status', 'fila'),
            ])
            ->whereHas('pelada.membros', fn ($query) => $query->where('user_id', $user->id))
            ->where('data_hora', '>=', now()->subHour())
            ->orderBy('data_hora')
            ->take(5)
            ->get();

        $solicitacoesBase = PeladaSolicitacao::with('pelada')
            ->where('user_id', $user->id)
            ->latest();

        $convites = (clone $solicitacoesBase)
            ->where('tipo_solicitacao', 'like', 'convite_%')
            ->get();

        $solicitacoes = (clone $solicitacoesBase)
            ->where(function ($query) {
                $query->whereNull('tipo_solicitacao')
                    ->orWhere('tipo_solicitacao', 'not like', 'convite_%');
            })
            ->get();

        $avaliacoesData = $this->avaliacoesData($request);
    
        return view('dashboard', [
            'aba' => $aba,
            'peladasOrganizadas' => $peladasOrganizadas,
            'membros' => $membros,
            'proximosJogos' => $proximosJogos,
            'participacoes' => $participacoes,
            'mensalistasCount' => $membros->where('tipo', 'mensalista')->where('status', 'ativo')->count(),
            'diaristasCount' => $membros->where('tipo', 'diarista')->where('status', 'ativo')->count(),
            'confirmacoesCount' => $participacoes->where('status', 'confirmado')->count(),
            'notificacoes' => $notificacoes,
            'notificacoesNaoLidasPainel' => $notificacoesNaoLidas,
            'feedPosts' => $feedPosts,
            'likedFeedPostIds' => $likedFeedPostIds,
            'postCategoryLabels' => PlayerPostController::categoryLabels(),
            'convites' => $convites,
            'solicitacoes' => $solicitacoes,
            'totalJogosProximos' => $membros->sum(fn ($membro) => $membro->pelada->jogos->count()),
            'convitesPendentes' => $convites->where('status', 'pendente')->count(),
            'solicitacoesPendentes' => $solicitacoes->where('status', 'pendente')->count(),
            ...$avaliacoesData,
        ]);
    }

    private function avaliacoesData(Request $request): array
    {
        $user = $request->user();
        $this->finalizarRodadasExpiradas();

        $jogos = PeladaJogo::with(['pelada.esporte', 'participantes.user.playerProfile', 'participantes.membro'])
            ->where('status', 'finalizado')
            ->whereNotNull('finalizada_em')
            ->whereBetween('finalizada_em', [now()->subDays(2), now()])
            ->whereHas('participantes', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', 'confirmado')
                ->where('presente_local', true)
                ->whereHas('membro', fn ($query) => $query->whereIn('tipo', ['mensalista', 'diarista'])))
            ->orderByDesc('finalizada_em')
            ->get();

        $jogoIds = $jogos->pluck('id');
        $avaliacoesFeitas = AvaliacaoPartida::query()
            ->whereIn('pelada_jogo_id', $jogoIds)
            ->where('avaliador_id', $user->id)
            ->get()
            ->mapWithKeys(fn ($avaliacao) => [$avaliacao->pelada_jogo_id.':'.$avaliacao->avaliado_id => $avaliacao]);

        $profileIdsByUserId = $jogos
            ->flatMap(fn (PeladaJogo $jogo) => $jogo->participantes)
            ->mapWithKeys(fn ($participante) => [
                $participante->user_id => $participante->user?->playerProfile?->id,
            ])
            ->filter();

        $votosFeitos = PlayerVote::query()
            ->whereIn('pelada_jogo_id', $jogoIds)
            ->where('voter_id', $user->id)
            ->whereIn('player_profile_id', $profileIdsByUserId->values())
            ->get(['pelada_jogo_id', 'player_profile_id', 'type'])
            ->groupBy(fn ($vote) => $vote->pelada_jogo_id.':'.$vote->player_profile_id)
            ->map(fn ($votes) => $votes->last()->type);

        $pendingGames = $jogos->map(function (PeladaJogo $jogo) use ($user, $avaliacoesFeitas, $profileIdsByUserId, $votosFeitos) {
            $presentes = $jogo->participantes
                ->filter(fn ($participante) => $participante->user_id && $participante->user_id !== $user->id)
                ->filter(fn ($participante) => $participante->status === 'confirmado')
                ->filter(fn ($participante) => $participante->presente_local)
                ->filter(fn ($participante) => in_array($participante->membro?->tipo, ['mensalista', 'diarista'], true))
                ->values();

            $avaliados = $presentes
                ->reject(fn ($participante) => $avaliacoesFeitas->has($jogo->id.':'.$participante->user_id))
                ->values();

            $votaveis = $presentes->map(function ($participante) use ($jogo, $profileIdsByUserId, $votosFeitos, $avaliacoesFeitas) {
                $profileId = $profileIdsByUserId->get($participante->user_id);
                $participante->voto_atual = $profileId
                    ? $votosFeitos->get($jogo->id.':'.$profileId)
                    : null;
                $participante->avaliacao_atual = $avaliacoesFeitas->get($jogo->id.':'.$participante->user_id);

                return $participante;
            });

            return (object) [
                'jogo' => $jogo,
                'avaliados' => $avaliados,
                'votaveis' => $votaveis,
            ];
        })->filter(fn ($item) => $item->avaliados->isNotEmpty() || $item->votaveis->isNotEmpty())->values();

        return [
            'pendingGames' => $pendingGames,
            'recebidas' => $user->avaliacoesRecebidas()->with(['avaliador', 'jogo.pelada'])->latest()->take(5)->get(),
            'feitas' => $user->avaliacoesFeitas()->with(['avaliado', 'jogo.pelada'])->latest()->take(5)->get(),
            'mediaRecebida' => $user->rating_average,
            'totalRecebidas' => $user->rating_count,
            'totalFeitas' => $user->avaliacoesFeitas()->count(),
            'voteTypes' => $this->voteTypes(),
        ];
    }

    private function voteTypes(): array
    {
        return [
            'craque' => ['label' => 'Craque da rodada', 'score' => 15],
            'garcom' => ['label' => 'Garçom', 'score' => 8],
            'muralha' => ['label' => 'Muralha', 'score' => 8],
            'fair_play' => ['label' => 'Fair play', 'score' => 8],
            'carcara' => ['label' => 'Carcara', 'score' => 8],
            'fominha' => ['label' => 'Fominha', 'score' => 4],
            'maestro' => ['label' => 'Maestro', 'score' => 10],
            'xerife' => ['label' => 'Xerife', 'score' => 10],
        ];
    }

    private function finalizarRodadasExpiradas(): void
    {
        PeladaJogo::query()
            ->where('data_hora', '<=', now()->subDay())
            ->whereIn('status', ['aberto', 'fechado', 'realizado'])
            ->update([
                'status' => 'finalizado',
                'finalizada_em' => now(),
                'cancelada_em' => null,
            ]);
    }
}
