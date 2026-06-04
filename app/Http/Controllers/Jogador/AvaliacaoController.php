<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Models\AvaliacaoPartida;
use App\Models\Notificacao;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PlayerAchievement;
use App\Models\PlayerProfile;
use App\Models\PlayerStat;
use App\Models\PlayerVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvaliacaoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $this->finalizarRodadasExpiradas();

        $jogos = PeladaJogo::with(['pelada.esporte', 'participantes.user.playerProfile'])
            ->where('status', 'finalizado')
            ->whereNotNull('finalizada_em')
            ->whereBetween('finalizada_em', [now()->subDays(2), now()])
            ->whereHas('participantes', fn ($query) => $query->where('user_id', $user->id)->where('presente_local', true))
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
                ->filter(fn ($participante) => $participante->presente_local)
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

        return view('jogador.avaliacoes.index', [
            'pendingGames' => $pendingGames,
            'recebidas' => $user->avaliacoesRecebidas()
                ->with(['avaliador', 'jogo.pelada'])
                ->latest()
                ->take(5)
                ->get(),
            'feitas' => $user->avaliacoesFeitas()
                ->with(['avaliado', 'jogo.pelada'])
                ->latest()
                ->take(5)
                ->get(),
            'mediaRecebida' => $user->rating_average,
            'totalRecebidas' => $user->rating_count,
            'totalFeitas' => $user->avaliacoesFeitas()->count(),
            'voteTypes' => $this->voteTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pelada_jogo_id' => ['required', 'exists:pelada_jogos,id'],
            'avaliado_id' => ['required', 'exists:users,id'],
            'estrelas' => ['required', 'integer', 'between:1,5'],
            'vote_type' => ['nullable', 'in:'.implode(',', array_keys($this->voteTypes()))],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $jogo = PeladaJogo::findOrFail($data['pelada_jogo_id']);

        if (! $jogo->avaliacoesAbertas()) {
            return back()->with('status', 'Avaliacoes ficam disponiveis por 2 dias apos a finalizacao da rodada.');
        }

        $participacao = PeladaJogoParticipante::where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $user->id)
            ->where('presente_local', true)
            ->first();

        if (! $participacao) {
            return back()->with('status', 'Somente jogadores presentes na partida podem avaliar.');
        }

        if ((int) $data['avaliado_id'] === $user->id) {
            return back()->with('status', 'Voce nao pode se avaliar.');
        }

        $avaliadoParticipacao = PeladaJogoParticipante::with('user')
            ->where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $data['avaliado_id'])
            ->where('presente_local', true)
            ->first();

        if (! $avaliadoParticipacao?->user) {
            return back()->with('status', 'O jogador avaliado precisa estar presente e cadastrado no sistema.');
        }

        $avaliacao = AvaliacaoPartida::where('pelada_jogo_id', $jogo->id)
            ->where('avaliador_id', $user->id)
            ->where('avaliado_id', $data['avaliado_id'])
            ->first();

        $isNew = ! $avaliacao;

        $avaliacao = AvaliacaoPartida::updateOrCreate(
            [
                'pelada_jogo_id' => $jogo->id,
                'avaliador_id' => $user->id,
                'avaliado_id' => $data['avaliado_id'],
            ],
            [
                'estrelas' => $data['estrelas'],
                'comentario' => $data['comentario'],
            ]
        );

        if ($isNew) {
            $user->addPoints(5, 'avaliou', 'Avaliacao emitida para partida '.$jogo->titulo, 'jogo:'.$jogo->id);
        }

        if (filled($data['vote_type'] ?? null)) {
            $this->syncVote($user, $jogo, $avaliadoParticipacao->user, $data['vote_type']);
        }

        $avaliado = $avaliadoParticipacao->user;
        if ($isNew && (int) $data['estrelas'] === 5) {
            $avaliado->addPoints(10, 'recebeu_5', 'Recebeu avaliacao 5 estrelas', 'jogo:'.$jogo->id);
        }

        $avaliado->refreshBadges();
        if ($isNew) {
            Notificacao::create([
                'user_id' => $avaliado->id,
                'titulo' => 'Voce recebeu uma nova avaliacao',
                'mensagem' => sprintf('%s avaliou voce com %s estrelas.', $user->name, $data['estrelas']),
                'link' => route('perfil.edit'),
            ]);
        }

        $user->refreshBadges();

        return back()->with('status', $isNew ? 'Avaliacao enviada com sucesso.' : 'Avaliacao atualizada com sucesso.');
    }

    public function vote(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pelada_jogo_id' => ['required', 'exists:pelada_jogos,id'],
            'voted_user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:'.implode(',', array_keys($this->voteTypes()))],
        ]);

        $user = $request->user();
        $jogo = PeladaJogo::with('pelada')->findOrFail($data['pelada_jogo_id']);

        if (! $jogo->avaliacoesAbertas()) {
            return back()->with('status', 'Votos ficam disponiveis por 2 dias apos a finalizacao da rodada.');
        }

        if ((int) $data['voted_user_id'] === $user->id) {
            return back()->with('status', 'Voce nao pode votar em si mesmo.');
        }

        $votantePresente = PeladaJogoParticipante::where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $user->id)
            ->where('presente_local', true)
            ->exists();

        $votadoParticipacao = PeladaJogoParticipante::with('user')
            ->where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $data['voted_user_id'])
            ->where('presente_local', true)
            ->first();

        if (! $votantePresente || ! $votadoParticipacao?->user) {
            return back()->with('status', 'Somente jogadores presentes na rodada podem votar e receber votos.');
        }

        $this->syncVote($user, $jogo, $votadoParticipacao->user, $data['type']);

        return back()->with('status', 'Voto registrado com sucesso.');
    }

    private function syncVote($user, PeladaJogo $jogo, $votedUser, string $type): void
    {
        $profile = PlayerProfile::ensureForUser($votedUser);

        $vote = PlayerVote::where('pelada_jogo_id', $jogo->id)
            ->where('voter_id', $user->id)
            ->where('player_profile_id', $profile->id)
            ->first();

        if ($vote && $vote->type === $type) {
            return;
        }

        $metadata = [
            'pelada_id' => $jogo->pelada_id,
            'pelada_nome' => $jogo->pelada?->nome,
            'jogo_titulo' => $jogo->titulo,
        ];

        if ($vote) {
            $this->removeVoteImpact($profile, $jogo, $vote->type);

            $vote->update([
                'type' => $type,
                'metadata' => $metadata,
            ]);

            $this->applyVoteImpact($profile, $jogo, $type, false);

            return;
        }

        PlayerVote::create([
            'player_profile_id' => $profile->id,
            'voter_id' => $user->id,
            'pelada_jogo_id' => $jogo->id,
            'type' => $type,
            'metadata' => $metadata,
        ]);

        $this->applyVoteImpact($profile, $jogo, $type);
        $user->addPoints(2, 'votou_destaque', 'Voto de destaque emitido na rodada '.$jogo->titulo, 'jogo:'.$jogo->id);

        Notificacao::create([
            'user_id' => $votedUser->id,
            'titulo' => 'Voce recebeu um voto de destaque',
            'mensagem' => sprintf('%s votou em voce como %s.', $user->name, $this->voteTypes()[$type]['label']),
            'link' => route('peladeiros.show', $profile),
        ]);
    }

    private function voteTypes(): array
    {
        return [
            'craque' => ['label' => 'Craque da rodada', 'score' => 15],
            'garcom' => ['label' => 'Garcom', 'score' => 8],
            'muralha' => ['label' => 'Muralha', 'score' => 8],
            'fair_play' => ['label' => 'Fair play', 'score' => 8],
            'carcara' => ['label' => 'Carcara', 'score' => 8],
            'fominha' => ['label' => 'Fominha', 'score' => 4],
            'maestro' => ['label' => 'Maestro', 'score' => 10],
            'xerife' => ['label' => 'Xerife', 'score' => 10],
        ];
    }

    private function applyVoteImpact(PlayerProfile $profile, PeladaJogo $jogo, string $type, bool $awardPoints = true): void
    {
        $voteType = $this->voteTypes()[$type];
        $score = $voteType['score'];

        if ($score > 0) {
            $profile->increment('reputation_score', $score);
            if ($awardPoints) {
                $profile->user?->addPoints($score, 'voto_'.$type, 'Recebeu voto: '.$voteType['label'], 'jogo:'.$jogo->id);
            }
        }

        $stat = PlayerStat::firstOrCreate([
            'player_profile_id' => $profile->id,
            'esporte_id' => $jogo->pelada?->esporte_id,
        ]);

        match ($type) {
            'craque' => $stat->increment('mvps'),
            'garcom', 'maestro' => $stat->increment('assistencias'),
            default => null,
        };

        $this->refreshVoteAchievements($profile);
    }

    private function removeVoteImpact(PlayerProfile $profile, PeladaJogo $jogo, string $type): void
    {
        $voteType = $this->voteTypes()[$type];
        $score = $voteType['score'];

        if ($score > 0) {
            $profile->decrement('reputation_score', min((int) $profile->reputation_score, $score));
        }

        $stat = PlayerStat::firstOrCreate([
            'player_profile_id' => $profile->id,
            'esporte_id' => $jogo->pelada?->esporte_id,
        ]);

        match ($type) {
            'craque' => $stat->decrement('mvps', min((int) $stat->mvps, 1)),
            'garcom', 'maestro' => $stat->decrement('assistencias', min((int) $stat->assistencias, 1)),
            default => null,
        };
    }

    private function refreshVoteAchievements(PlayerProfile $profile): void
    {
        $craques = $profile->votes()->where('type', 'craque')->count();
        $fairPlay = $profile->votes()->where('type', 'fair_play')->count();

        if ($craques >= 3) {
            PlayerAchievement::firstOrCreate([
                'player_profile_id' => $profile->id,
                'key' => 'craque_3x',
            ], [
                'title' => 'Craque 3x',
                'description' => 'Recebeu pelo menos 3 votos de craque da rodada.',
                'earned_at' => now(),
            ]);
        }

        if ($fairPlay >= 3) {
            PlayerAchievement::firstOrCreate([
                'player_profile_id' => $profile->id,
                'key' => 'resenha_limpa',
            ], [
                'title' => 'Resenha limpa',
                'description' => 'Recebeu pelo menos 3 votos de fair play.',
                'earned_at' => now(),
            ]);
        }
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
