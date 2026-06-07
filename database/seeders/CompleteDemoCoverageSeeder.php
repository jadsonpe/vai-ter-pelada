<?php

namespace Database\Seeders;

use App\Models\Avaliacao;
use App\Models\AvaliacaoPartida;
use App\Models\Notificacao;
use App\Models\Pelada;
use App\Models\PeladaCaixaMovimentacao;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use App\Models\PlayerAchievement;
use App\Models\PlayerFollow;
use App\Models\PlayerProfile;
use App\Models\PlayerRanking;
use App\Models\PlayerSocialLink;
use App\Models\PlayerStat;
use App\Models\PlayerVote;
use App\Models\Presenca;
use App\Models\Sorteio;
use App\Models\SorteioSobra;
use App\Models\SorteioTime;
use App\Models\SorteioTimeJogador;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteDemoCoverageSeeder extends Seeder
{
    private array $voteTypes = ['craque', 'garcom', 'muralha', 'fair_play', 'carcara', 'fominha', 'maestro', 'xerife'];

    public function run(): void
    {
        $this->normalizeUsers();
        $this->ensurePlayerProfiles();
        $this->ensureCompletedRoundsWithEverything();
        $this->mirrorLegacyRatings();
        $this->ensureSocialGraph();
        $this->ensureNotifications();
    }

    private function normalizeUsers(): void
    {
        User::query()->update(['password' => Hash::make('vaiterpelada11')]);

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'apelido' => 'Admin',
                'password' => Hash::make('vaiterpelada11'),
                'role' => 'admin',
                'status' => 'ativo',
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    private function ensurePlayerProfiles(): void
    {
        $userCount = User::count();
        if (
            $userCount > 0
            && PlayerProfile::count() >= $userCount
            && PlayerSocialLink::count() >= ($userCount * 4)
            && UserPoint::count() >= $userCount
            && UserBadge::count() >= $userCount
            && PlayerRanking::count() >= $userCount
        ) {
            return;
        }

        $users = User::with('playerProfile')->orderBy('id')->get();

        foreach ($users as $index => $user) {
            $profile = PlayerProfile::where('user_id', $user->id)->first()
                ?: PlayerProfile::ensureForUser($user);

            $profile->update([
                'headline' => $profile->headline ?: 'Bola no pe e resenha depois do jogo.',
                'bio' => $profile->bio ?: 'Perfil demo completo para testar compartilhamento, seguidores, votos e reputacao.',
                'nivel_label' => $profile->reputation_score > 0 ? $profile->nivel_label : 'Novato',
                'publico' => true,
            ]);

            foreach (['instagram', 'tiktok', 'youtube', 'whatsapp'] as $platform) {
                PlayerSocialLink::updateOrCreate(
                    ['player_profile_id' => $profile->id, 'platform' => $platform],
                    ['url' => $this->socialUrl($platform, $profile->slug, $user->phone)]
                );
            }

            UserPoint::firstOrCreate(
                ['user_id' => $user->id, 'origem' => 'seed_completa', 'referencia' => 'user:'.$user->id],
                ['valor' => 25 + ($index % 80), 'descricao' => 'Pontuacao demo da carga completa.']
            );

            UserBadge::firstOrCreate(
                ['user_id' => $user->id, 'badge_key' => 'perfil_completo'],
                ['nome' => 'Perfil completo', 'descricao' => 'Usuario preparado pela seed completa.', 'conquistado_em' => now()]
            );

            PlayerAchievement::firstOrCreate(
                ['player_profile_id' => $profile->id, 'key' => 'perfil_publico'],
                ['title' => 'Perfil publico', 'description' => 'Perfil esportivo publicado.', 'earned_at' => now()]
            );

            PlayerRanking::updateOrCreate(
                ['player_profile_id' => $profile->id, 'period' => 'geral', 'category' => 'reputacao'],
                [
                    'score' => max(1, $profile->reputation_score),
                    'position' => $index + 1,
                    'starts_at' => now()->startOfYear()->toDateString(),
                    'ends_at' => now()->endOfYear()->toDateString(),
                ]
            );
        }
    }

    private function ensureCompletedRoundsWithEverything(): void
    {
        if (
            PlayerVote::count() >= 1000
            && SorteioSobra::count() >= 50
            && Avaliacao::count() >= 300
            && AvaliacaoPartida::count() >= 1000
        ) {
            return;
        }

        Pelada::query()
            ->with(['membros' => fn ($query) => $query->with('user.playerProfile')->whereNotNull('user_id')->where('status', 'ativo')->orderBy('prioridade')])
            ->whereHas('membros')
            ->orderBy('id')
            ->limit(12)
            ->get()
            ->each(function (Pelada $pelada, int $index) {
                $this->ensureThirtyMembers($pelada);
                $pelada->load(['membros' => fn ($query) => $query->with('user.playerProfile')->whereNotNull('user_id')->where('status', 'ativo')->orderBy('prioridade')]);

                $date = now()->subDays(($index % 2) + 1)->setTime(20, 0);
                $jogo = PeladaJogo::updateOrCreate(
                    ['pelada_id' => $pelada->id, 'titulo' => 'Rodada completa demo'],
                    [
                        'data_hora' => $date,
                        'data_jogo' => $date->toDateString(),
                        'horario' => $date->format('H:i:s'),
                        'capacidade' => 30,
                        'vagas_totais' => 30,
                        'vagas_diaristas' => 0,
                        'status' => 'realizado',
                        'observacao' => 'Rodada completa para testar presença, sorteio, votos, caixa e avaliações.',
                    ]
                );

                $participants = $this->ensureParticipantsAndPresence($jogo, $pelada->membros->take(30));
                $this->ensureSorteio($jogo, $participants);
                $this->ensureFinance($jogo, $participants);
                $this->ensureRatingsAndVotes($jogo, $participants);
            });
    }

    private function ensureThirtyMembers(Pelada $pelada): void
    {
        $users = User::query()
            ->where('status', '!=', 'bloqueado')
            ->orderBy('id')
            ->get()
            ->sortBy(fn (User $user) => crc32($pelada->id.'-'.$user->id))
            ->take(30)
            ->values();

        if ($users->isEmpty()) {
            return;
        }

        if (! $users->contains('id', $pelada->organizador_id)) {
            $organizer = User::find($pelada->organizador_id);
            if ($organizer) {
                $users->pop();
                $users->prepend($organizer);
            }
        }

        foreach ($users as $position => $user) {
            PeladaMembro::updateOrCreate(
                ['pelada_id' => $pelada->id, 'user_id' => $user->id],
                [
                    'apelido' => $user->apelido,
                    'tipo' => 'mensalista',
                    'status' => 'ativo',
                    'prioridade' => $position + 1,
                    'data_entrada' => now()->subDays(90 - min($position, 29))->toDateString(),
                    'mensalista_desde' => now()->subDays(90 - min($position, 29))->toDateString(),
                    'observacao' => 'Mensalista demo da seed completa.',
                ]
            );
        }
    }

    private function ensureParticipantsAndPresence(PeladaJogo $jogo, $members)
    {
        return $members->values()->map(function (PeladaMembro $member, int $position) use ($jogo) {
            $participant = PeladaJogoParticipante::updateOrCreate(
                ['pelada_jogo_id' => $jogo->id, 'user_id' => $member->user_id],
                [
                    'pelada_membro_id' => $member->id,
                    'tipo' => 'mensalista',
                    'tipo_no_jogo' => 'mensalista',
                    'status' => 'confirmado',
                    'ordem_chegada' => $position + 1,
                    'posicao_fila' => null,
                    'confirmado_em' => $jogo->data_hora->copy()->subHours(2)->addMinutes($position),
                    'cancelado_em' => null,
                    'presente_local' => true,
                    'ordem_presenca' => $position + 1,
                ]
            );

            Presenca::updateOrCreate(
                ['pelada_jogo_id' => $jogo->id, 'user_id' => $member->user_id],
                [
                    'status' => 'compareceu',
                    'marcado_por' => $jogo->pelada->organizador_id,
                    'observacao' => 'Presenca confirmada pela seed completa.',
                ]
            );

            return $participant;
        });
    }

    private function ensureSorteio(PeladaJogo $jogo, $participants): void
    {
        $sorteio = Sorteio::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'criado_por' => $jogo->pelada->organizador_id],
            [
                'tipo_sorteio' => 'simples',
                'quantidade_times' => 2,
                'jogadores_por_time' => 5,
                'usar_ordem_manual' => false,
                'status' => 'publicado',
                'realizado_em' => $jogo->data_hora->copy()->subMinutes(30),
            ]
        );

        for ($teamNumber = 1; $teamNumber <= 2; $teamNumber++) {
            $time = SorteioTime::updateOrCreate(
                ['sorteio_id' => $sorteio->id, 'nome' => 'Time '.$teamNumber],
                ['nome_time' => 'Time '.$teamNumber, 'ordem' => $teamNumber]
            );

            foreach ($participants->take(10) as $position => $participant) {
                if ($position % 2 !== $teamNumber - 1) {
                    continue;
                }

                SorteioTimeJogador::updateOrCreate(
                    ['sorteio_time_id' => $time->id, 'user_id' => $participant->user_id],
                    ['pelada_jogo_participante_id' => $participant->id, 'ordem' => $position + 1]
                );
            }
        }

        foreach ($participants->slice(10) as $position => $participant) {
            SorteioSobra::updateOrCreate(
                ['sorteio_id' => $sorteio->id, 'user_id' => $participant->user_id],
                ['ordem' => $position + 1]
            );
        }
    }

    private function ensureFinance(PeladaJogo $jogo, $participants): void
    {
        $pelada = $jogo->pelada;
        $competencia = $jogo->data_hora->copy()->startOfMonth()->toDateString();

        foreach ($participants->take(8) as $participant) {
            PeladaCaixaMovimentacao::updateOrCreate(
                [
                    'pelada_id' => $pelada->id,
                    'pelada_jogo_id' => $jogo->id,
                    'pelada_jogo_participante_id' => $participant->id,
                    'tipo' => 'entrada',
                    'categoria' => 'mensalidade',
                ],
                [
                    'pelada_membro_id' => $participant->pelada_membro_id,
                    'user_id' => $participant->user_id,
                    'registrado_por' => $pelada->organizador_id,
                    'descricao' => 'Mensalidade demo do jogador',
                    'valor' => $pelada->valor_mensalista ?: 100,
                    'data_pagamento' => $jogo->data_hora->toDateString(),
                    'competencia' => $competencia,
                    'forma_pagamento' => 'PIX',
                    'observacao' => 'Lancamento vinculado ao participante.',
                ]
            );
        }

        foreach ([['saida', 'aluguel', 350], ['saida', 'material', 90], ['entrada', 'diarista', 180]] as $entry) {
            PeladaCaixaMovimentacao::updateOrCreate(
                ['pelada_id' => $pelada->id, 'pelada_jogo_id' => $jogo->id, 'tipo' => $entry[0], 'categoria' => $entry[1]],
                [
                    'user_id' => $pelada->organizador_id,
                    'registrado_por' => $pelada->organizador_id,
                    'descricao' => 'Movimentacao demo: '.$entry[1],
                    'valor' => $entry[2],
                    'data_pagamento' => $jogo->data_hora->toDateString(),
                    'competencia' => $competencia,
                    'forma_pagamento' => $entry[0] === 'entrada' ? 'PIX' : 'Dinheiro',
                    'observacao' => 'Lancamento geral da rodada.',
                ]
            );
        }
    }

    private function ensureRatingsAndVotes(PeladaJogo $jogo, $participants): void
    {
        $participants = $participants->whereNotNull('user_id')->values();

        foreach ($participants->take(18) as $position => $participant) {
            $targets = $participants
                ->filter(fn ($target) => $target->user_id !== $participant->user_id)
                ->values()
                ->take($position < 9 ? 6 : 2);

            foreach ($targets as $targetIndex => $target) {
                $stars = 3 + (($position + $targetIndex) % 3);

                AvaliacaoPartida::updateOrCreate(
                    ['pelada_jogo_id' => $jogo->id, 'avaliador_id' => $participant->user_id, 'avaliado_id' => $target->user_id],
                    ['estrelas' => $stars, 'comentario' => 'Avaliacao demo completa da rodada.']
                );

                Avaliacao::updateOrCreate(
                    ['pelada_jogo_id' => $jogo->id, 'avaliador_id' => $participant->user_id, 'avaliado_id' => $target->user_id],
                    ['nota' => $stars, 'comentario' => 'Avaliacao legada demo completa da rodada.']
                );
            }

            foreach ($this->voteTypes as $typeIndex => $type) {
                $target = $participants->get(($position + $typeIndex + 1) % $participants->count());

                if (! $target || $target->user_id === $participant->user_id || ! $target->user?->playerProfile) {
                    continue;
                }

                PlayerVote::updateOrCreate(
                    [
                        'player_profile_id' => $target->user->playerProfile->id,
                        'voter_id' => $participant->user_id,
                        'pelada_jogo_id' => $jogo->id,
                        'type' => $type,
                    ],
                    [
                        'metadata' => [
                            'pelada_id' => $jogo->pelada_id,
                            'pelada_nome' => $jogo->pelada?->nome,
                            'jogo_titulo' => $jogo->titulo,
                            'demo' => true,
                        ],
                    ]
                );
            }
        }

        foreach ($participants as $participant) {
            $profile = $participant->user?->playerProfile;

            if (! $profile) {
                continue;
            }

            $voteCount = PlayerVote::where('player_profile_id', $profile->id)->count();
            $profile->update([
                'reputation_score' => max($profile->reputation_score, $voteCount * 5),
                'nivel_label' => $voteCount > 0 ? 'Peladeiro' : 'Novato',
            ]);

            PlayerStat::updateOrCreate(
                ['player_profile_id' => $profile->id, 'esporte_id' => $jogo->pelada?->esporte_id],
                [
                    'jogos' => 1 + ($profile->id % 30),
                    'vitorias' => $profile->id % 12,
                    'gols' => ($profile->id * 3) % 40,
                    'assistencias' => ($profile->id * 2) % 30,
                    'mvps' => PlayerVote::where('player_profile_id', $profile->id)->where('type', 'craque')->count(),
                    'sequencia_vitorias' => $profile->id % 5,
                    'aproveitamento' => 45 + ($profile->id % 45),
                ]
            );
        }
    }

    private function mirrorLegacyRatings(): void
    {
        if (Avaliacao::count() >= 300) {
            return;
        }

        AvaliacaoPartida::query()->chunkById(200, function ($ratings) {
            foreach ($ratings as $rating) {
                Avaliacao::updateOrCreate(
                    ['pelada_jogo_id' => $rating->pelada_jogo_id, 'avaliador_id' => $rating->avaliador_id, 'avaliado_id' => $rating->avaliado_id],
                    ['nota' => $rating->estrelas, 'comentario' => $rating->comentario]
                );
            }
        });
    }

    private function ensureSocialGraph(): void
    {
        $players = User::where('role', 'jogador')->where('status', 'ativo')->orderBy('id')->get()->values();

        if ($players->count() < 2) {
            return;
        }

        if (PlayerFollow::count() >= ($players->count() * 5)) {
            return;
        }

        foreach ($players as $index => $player) {
            for ($offset = 1; $offset <= 5; $offset++) {
                $followed = $players->get(($index + $offset) % $players->count());

                if ($followed && $followed->id !== $player->id) {
                    PlayerFollow::firstOrCreate(['follower_id' => $player->id, 'followed_id' => $followed->id]);
                }
            }
        }
    }

    private function ensureNotifications(): void
    {
        if (Notificacao::count() >= 80) {
            return;
        }

        User::query()->orderBy('id')->limit(80)->get()->each(function (User $user, int $index) {
            Notificacao::updateOrCreate(
                ['user_id' => $user->id, 'titulo' => 'Notificacao demo completa'],
                [
                    'mensagem' => 'Mensagem para testar central de notificacoes.',
                    'link' => $index % 2 === 0 ? '/peladas' : '/perfil',
                    'lida_em' => $index % 3 === 0 ? now()->subDay() : null,
                ]
            );
        });
    }

    private function socialUrl(string $platform, string $slug, ?string $phone): string
    {
        return match ($platform) {
            'instagram' => 'https://instagram.com/'.$slug,
            'tiktok' => 'https://tiktok.com/@'.$slug,
            'youtube' => 'https://youtube.com/@'.$slug,
            'whatsapp' => 'https://wa.me/'.(preg_replace('/\D+/', '', (string) $phone) ?: '5581999999999'),
        };
    }
}
