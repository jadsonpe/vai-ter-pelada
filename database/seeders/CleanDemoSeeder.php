<?php

namespace Database\Seeders;

use App\Models\AvaliacaoPartida;
use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaJogoParticipanteEstatistica;
use App\Models\PeladaMembro;
use App\Models\PlayerAchievement;
use App\Models\PlayerProfile;
use App\Models\PlayerRanking;
use App\Models\PlayerSocialLink;
use App\Models\PlayerStat;
use App\Models\PlayerVote;
use App\Models\Torneio;
use App\Models\TorneioCartao;
use App\Models\TorneioGol;
use App\Models\TorneioJogo;
use App\Models\TorneioParticipante;
use App\Models\TorneioTime;
use App\Models\TorneioTimeJogador;
use App\Models\User;
use App\Models\UserEsportePerfil;
use App\Models\UserPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CleanDemoSeeder extends Seeder
{
    private const PASSWORD = 'vaiterpelada11';

    private array $voteScores = [
        'craque' => 15,
        'garcom' => 8,
        'muralha' => 8,
        'fair_play' => 8,
        'carcara' => 8,
        'fominha' => 4,
        'maestro' => 10,
        'xerife' => 10,
    ];

    public function run(): void
    {
        $this->cleanDatabase();

        $sports = $this->createSports();
        $users = $this->createUsers($sports);
        $peladas = $this->createPeladas($users, $sports);

        $this->createPeladaGames($peladas, $users);
        $this->createTournaments($peladas, $users);
        $this->refreshProfileSummaries($users);
        $this->call(PlayerPostsDemoSeeder::class);
    }

    private function cleanDatabase(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'torneio_cartoes',
            'torneio_gols',
            'torneio_jogos',
            'torneio_grupo_times',
            'torneio_grupos',
            'torneio_time_jogadores',
            'torneio_times',
            'torneio_participantes',
            'torneios',
            'avaliacoes_partidas',
            'avaliacoes',
            'player_post_likes',
            'player_posts',
            'player_votes',
            'player_rankings',
            'player_achievements',
            'player_stats',
            'player_social_links',
            'player_follows',
            'user_badges',
            'user_points',
            'user_esporte_perfis',
            'notificacoes',
            'reports',
            'presencas',
            'sorteio_time_jogadores',
            'sorteio_sobras',
            'sorteio_times',
            'sorteios',
            'pelada_jogo_participante_estatisticas',
            'pelada_jogo_participantes',
            'pelada_caixa_movimentacoes',
            'pelada_solicitacoes',
            'pelada_membros',
            'pelada_jogos',
            'peladas',
            'player_profiles',
            'users',
            'esportes',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function createSports(): array
    {
        return collect([
            ['nome' => 'Futebol', 'slug' => 'futebol'],
            ['nome' => 'Futsal', 'slug' => 'futsal'],
            ['nome' => 'Society', 'slug' => 'society'],
        ])->mapWithKeys(fn (array $sport) => [
            $sport['slug'] => Esporte::create($sport + ['ativo' => true]),
        ])->all();
    }

    private function createUsers(array $sports)
    {
        $names = [
            'Administrador Demo', 'Bruno Silva', 'Carlos Andrade', 'Diego Souza', 'Eduardo Lima',
            'Felipe Rocha', 'Gabriel Martins', 'Henrique Costa', 'Igor Santos', 'Joao Pereira',
            'Kaue Ribeiro', 'Lucas Almeida', 'Marcelo Nunes', 'Nicolas Barros', 'Otavio Freitas',
            'Paulo Teixeira', 'Rafael Gomes', 'Samuel Moreira', 'Thiago Carvalho', 'Victor Araujo',
            'Wesley Lopes', 'Yuri Fernandes', 'Andre Moraes', 'Cesar Batista', 'Daniel Cardoso',
            'Enzo Duarte', 'Fabio Monteiro', 'Gustavo Vieira', 'Leandro Ramos', 'Renato Campos',
        ];

        $positions = ['Goleiro', 'Zagueiro', 'Lateral Direito', 'Volante', 'Meia Central', 'Ponta Direita', 'Centroavante', 'Fixo', 'Ala Esquerdo', 'Pivô'];
        $sportList = array_values($sports);

        return collect($names)->map(function (string $name, int $index) use ($positions, $sportList) {
            $isAdmin = $index === 0;
            $user = User::forceCreate([
                'name' => $name,
                'apelido' => explode(' ', $name)[0],
                'email' => $isAdmin ? 'admin@admin.com' : sprintf('jogador%02d@vaiterpelada.test', $index),
                'email_verified_at' => now(),
                'password' => Hash::make(self::PASSWORD),
                'role' => $isAdmin ? 'admin' : ($index <= 4 ? 'organizador' : 'jogador'),
                'status' => 'ativo',
                'active' => true,
                'plano' => $index <= 4 ? 'pro' : 'gratis',
                'limite_peladas' => $index <= 4 ? 0 : 1,
                'phone' => sprintf('(11) 9%04d-%04d', 1000 + $index, 2000 + $index),
                'data_nascimento' => now()->subYears(18 + ($index % 20))->subDays($index * 11),
                'cidade' => ['São Paulo', 'Guarulhos', 'Osasco', 'Santo André'][$index % 4],
                'bairro' => ['Centro', 'Vila Madalena', 'Mooca', 'Tatuapé', 'Pinheiros'][$index % 5],
                'estado' => 'SP',
                'nivel' => min(5, 1 + intdiv($index, 6)),
            ]);

            $mainSport = $sportList[$index % count($sportList)];
            $profile = PlayerProfile::create([
                'user_id' => $user->id,
                'slug' => PlayerProfile::uniqueSlug($user->apelido ?: $user->name),
                'esporte_principal_id' => $mainSport->id,
                'posicao_favorita' => $positions[$index % count($positions)],
                'nivel_label' => 'Novato',
                'headline' => 'Jogo coletivo, compromisso e resenha depois da partida.',
                'bio' => 'Peladeiro ativo nas rodadas da semana, sempre pronto para completar o time e disputar torneios.',
                'publico' => true,
                'banner_theme' => ['verde_campo', 'noturno', 'quadra', 'ouro'][$index % 4],
            ]);

            PlayerSocialLink::create([
                'player_profile_id' => $profile->id,
                'platform' => 'instagram',
                'url' => 'https://instagram.com/'.Str::slug($user->apelido ?: $user->name, ''),
            ]);

            foreach ($sportList as $offset => $sport) {
                UserEsportePerfil::create([
                    'user_id' => $user->id,
                    'esporte_id' => $sport->id,
                    'posicao' => $positions[($index + $offset) % count($positions)],
                ]);
            }

            return $user;
        })->values();
    }

    private function createPeladas($users, array $sports)
    {
        $peladaNames = [
            'Quarta do Centro',
            'Futsal dos Amigos',
            'Society Prime',
            'Domingo Raiz',
            'Liga da Firma',
            'Arena Noturna',
        ];

        return collect($peladaNames)->map(function (string $name, int $index) use ($users, $sports) {
            $sport = array_values($sports)[$index % count($sports)];
            $organizador = $users[$index % 5];

            $pelada = Pelada::create([
                'organizador_id' => $organizador->id,
                'esporte_id' => $sport->id,
                'nome' => $name,
                'slug' => Str::slug($name),
                'descricao' => 'Pelada demo com elenco completo, rodadas passadas, futuras e estatísticas.',
                'data_fundacao' => now()->subMonths(10 + $index),
                'categoria' => 'adulto',
                'cidade' => 'São Paulo',
                'bairro' => ['Centro', 'Mooca', 'Pinheiros', 'Tatuapé', 'Ipiranga', 'Santana'][$index],
                'local_nome' => 'Arena '.$name,
                'endereco' => 'Rua Demo, '.(100 + $index),
                'local' => 'Arena '.$name,
                'dia_semana' => $index % 7,
                'horario' => sprintf('%02d:00', 19 + ($index % 3)),
                'vagas_totais' => 30,
                'vagas_diaristas' => 8,
                'aceita_diarista' => true,
                'requer_aprovacao' => false,
                'capacidade' => 30,
                'valor_mensalista' => 120,
                'valor_diarista' => 25,
                'status' => 'ativa',
                'ativa' => true,
                'regras' => 'Chegar 15 minutos antes, colete obrigatório e respeito aos colegas.',
                'whatsapp_contato' => '(11) 99999-0000',
            ]);

            foreach ($users as $order => $user) {
                PeladaMembro::create([
                    'pelada_id' => $pelada->id,
                    'user_id' => $user->id,
                    'apelido' => $user->apelido,
                    'tipo' => $order % 4 === 0 ? 'diarista' : 'mensalista',
                    'status' => 'ativo',
                    'prioridade' => $order + 1,
                    'data_entrada' => now()->subMonths(6)->addDays($order),
                    'mensalista_desde' => now()->subMonths(5)->addDays($order),
                ]);
            }

            return $pelada;
        })->values();
    }

    private function createPeladaGames($peladas, $users): void
    {
        $voteTypes = array_keys($this->voteScores);

        foreach ($peladas as $peladaIndex => $pelada) {
            $members = $pelada->membros()->get()->keyBy('user_id');

            for ($round = 1; $round <= 4; $round++) {
                $date = now()->subWeeks(6 - $round)->subDays($peladaIndex);
                $jogo = $this->createPeladaJogo($pelada, "Rodada {$round}", $date, 'finalizado');

                $participantes = collect();
                foreach ($users as $userIndex => $user) {
                    $participantes->push($participante = PeladaJogoParticipante::create([
                        'pelada_jogo_id' => $jogo->id,
                        'user_id' => $user->id,
                        'pelada_membro_id' => $members[$user->id]->id,
                        'tipo' => $members[$user->id]->tipo,
                        'tipo_no_jogo' => $members[$user->id]->tipo,
                        'status' => 'confirmado',
                        'ordem_chegada' => $userIndex + 1,
                        'presente_local' => true,
                        'ordem_presenca' => $userIndex + 1,
                        'confirmado_em' => $date->copy()->subDays(2),
                    ]));

                    PeladaJogoParticipanteEstatistica::create([
                        'pelada_jogo_id' => $jogo->id,
                        'pelada_jogo_participante_id' => $participante->id,
                        'user_id' => $user->id,
                        'gols' => ($userIndex + $round + $peladaIndex) % 4 === 0 ? 2 : (($userIndex + $round) % 3 === 0 ? 1 : 0),
                        'cartoes_amarelos' => ($userIndex + $round) % 9 === 0 ? 1 : 0,
                        'cartoes_vermelhos' => ($userIndex + $round + $peladaIndex) % 23 === 0 ? 1 : 0,
                        'cartoes_azuis' => ($userIndex + $round) % 17 === 0 ? 1 : 0,
                    ]);
                }

                foreach ($users as $userIndex => $avaliador) {
                    for ($offset = 1; $offset <= 3; $offset++) {
                        $avaliado = $users[($userIndex + $offset + $round) % $users->count()];
                        AvaliacaoPartida::create([
                            'pelada_jogo_id' => $jogo->id,
                            'avaliador_id' => $avaliador->id,
                            'avaliado_id' => $avaliado->id,
                            'estrelas' => 3 + (($userIndex + $offset + $round + $peladaIndex) % 3),
                            'comentario' => $offset === 1 ? 'Boa presença, ajudou bastante a equipe.' : null,
                            'created_at' => $jogo->finalizada_em->copy()->addHours($offset),
                            'updated_at' => $jogo->finalizada_em->copy()->addHours($offset),
                        ]);
                    }

                    $votedUser = $users[($userIndex + $round + 1) % $users->count()];
                    $type = $voteTypes[($userIndex + $round + $peladaIndex) % count($voteTypes)];
                    $this->createVote($jogo, $avaliador, $votedUser, $type);
                }
            }

            for ($round = 1; $round <= 2; $round++) {
                $this->createPeladaJogo($pelada, "Próxima rodada {$round}", now()->addWeeks($round + $peladaIndex), 'aberto');
            }
        }
    }

    private function createPeladaJogo(Pelada $pelada, string $title, Carbon $date, string $status): PeladaJogo
    {
        return PeladaJogo::create([
            'pelada_id' => $pelada->id,
            'titulo' => $title,
            'data_hora' => $date,
            'data_jogo' => $date->toDateString(),
            'horario' => $date->format('H:i:s'),
            'capacidade' => 30,
            'vagas_totais' => 30,
            'vagas_diaristas' => 8,
            'status' => $status,
            'finalizada_em' => $status === 'finalizado' ? $date->copy()->addHours(2) : null,
            'cancelada_em' => null,
            'observacao' => $status === 'finalizado' ? 'Rodada demo finalizada com avaliações.' : 'Rodada futura demo.',
        ]);
    }

    private function createVote(PeladaJogo $jogo, User $voter, User $votedUser, string $type): void
    {
        $profile = PlayerProfile::ensureForUser($votedUser);

        PlayerVote::create([
            'player_profile_id' => $profile->id,
            'voter_id' => $voter->id,
            'pelada_jogo_id' => $jogo->id,
            'type' => $type,
            'metadata' => [
                'pelada_id' => $jogo->pelada_id,
                'pelada_nome' => $jogo->pelada?->nome,
                'jogo_titulo' => $jogo->titulo,
            ],
            'created_at' => $jogo->finalizada_em?->copy()->addHour(),
            'updated_at' => $jogo->finalizada_em?->copy()->addHour(),
        ]);

        $score = $this->voteScores[$type] ?? 0;
        $profile->increment('reputation_score', $score);

        if ($score > 0) {
            UserPoint::create([
                'user_id' => $votedUser->id,
                'valor' => $score,
                'origem' => 'voto_'.$type,
                'descricao' => 'Recebeu voto de destaque em rodada demo.',
                'referencia' => 'jogo:'.$jogo->id,
            ]);
        }
    }

    private function createTournaments($peladas, $users): void
    {
        foreach ($peladas->take(3) as $index => $pelada) {
            $torneio = Torneio::create([
                'pelada_id' => $pelada->id,
                'nome' => 'Copa '.$pelada->nome,
                'slug' => Str::slug('copa '.$pelada->nome),
                'data_torneio' => now()->subMonths(2 - $index)->toDateString(),
                'jogadores_por_time' => 5,
                'quantidade_times' => 6,
                'formato' => 'pontos_corridos',
                'tipo_confronto' => 'ida',
                'quantidade_grupos' => 1,
                'classificados_total' => 4,
                'classificados_por_grupo' => 4,
                'tipo_confronto_mata_mata' => 'unico',
                'tipo_confronto_final' => 'unico',
                'terceiro_lugar' => true,
                'wo_gols_vencedor' => 3,
                'wo_gols_perdedor' => 0,
                'wo_conta_saldo' => true,
                'status' => 'finalizado',
                'regras' => 'Torneio demo com súmulas completas.',
            ]);

            $members = $pelada->membros()->get()->keyBy('user_id');
            $participantes = $users->mapWithKeys(fn (User $user, int $userIndex) => [
                $user->id => TorneioParticipante::create([
                    'torneio_id' => $torneio->id,
                    'user_id' => $user->id,
                    'pelada_membro_id' => $members[$user->id]->id,
                    'tipo' => 'membro',
                    'goleiro' => $userIndex % 10 === 0,
                    'cabeca_chave' => $userIndex < 6,
                    'status' => 'ativo',
                ]),
            ]);

            $teams = collect(range(1, 6))->map(fn (int $teamNumber) => TorneioTime::create([
                'torneio_id' => $torneio->id,
                'nome' => 'Time '.$teamNumber,
                'ordem' => $teamNumber,
            ]));

            foreach ($users as $userIndex => $user) {
                TorneioTimeJogador::create([
                    'torneio_time_id' => $teams[intdiv($userIndex, 5)]->id,
                    'torneio_participante_id' => $participantes[$user->id]->id,
                    'ordem' => ($userIndex % 5) + 1,
                ]);
            }

            $pairs = [[0, 1], [2, 3], [4, 5], [0, 2], [1, 4], [3, 5]];
            foreach ($pairs as $gameIndex => [$a, $b]) {
                $golsA = 1 + (($gameIndex + $index) % 4);
                $golsB = ($gameIndex + $index + 2) % 4;
                $jogo = TorneioJogo::create([
                    'torneio_id' => $torneio->id,
                    'time_a_id' => $teams[$a]->id,
                    'time_b_id' => $teams[$b]->id,
                    'fase' => $gameIndex === count($pairs) - 1 ? 'final' : 'pontos_corridos',
                    'rodada' => $gameIndex + 1,
                    'ordem' => $gameIndex + 1,
                    'gols_a' => $golsA,
                    'gols_b' => $golsB,
                    'vencedor_id' => $golsA >= $golsB ? $teams[$a]->id : $teams[$b]->id,
                    'decidido_penaltis' => $golsA === $golsB,
                    'wo' => false,
                    'status' => 'finalizado',
                    'observacao' => 'Súmula demo completa.',
                ]);

                $this->createTournamentStats($jogo, $teams[$a], $golsA, $gameIndex);
                $this->createTournamentStats($jogo, $teams[$b], $golsB, $gameIndex + 3);
            }
        }
    }

    private function createTournamentStats(TorneioJogo $jogo, TorneioTime $team, int $goals, int $seed): void
    {
        $players = $team->jogadores()->with('participante')->get()->values();

        for ($i = 0; $i < $goals; $i++) {
            $player = $players[($i + $seed) % $players->count()];
            TorneioGol::create([
                'torneio_jogo_id' => $jogo->id,
                'torneio_time_id' => $team->id,
                'torneio_participante_id' => $player->torneio_participante_id,
                'quantidade' => 1,
            ]);
        }

        foreach (['amarelo', 'vermelho', 'azul'] as $offset => $type) {
            if (($seed + $offset) % 2 !== 0) {
                continue;
            }

            $player = $players[($seed + $offset) % $players->count()];
            TorneioCartao::create([
                'torneio_jogo_id' => $jogo->id,
                'torneio_time_id' => $team->id,
                'torneio_participante_id' => $player->torneio_participante_id,
                'tipo' => $type,
                'quantidade' => 1,
            ]);
        }
    }

    private function refreshProfileSummaries($users): void
    {
        foreach ($users as $user) {
            $profile = PlayerProfile::ensureForUser($user);
            $profile->forceFill([
                'nivel_label' => PlayerProfile::levelForScore((int) $profile->reputation_score),
            ])->save();

            foreach ($user->esportePerfis as $perfil) {
                PlayerStat::updateOrCreate(
                    ['player_profile_id' => $profile->id, 'esporte_id' => $perfil->esporte_id],
                    [
                        'jogos' => PeladaJogoParticipanteEstatistica::where('user_id', $user->id)
                            ->whereHas('jogo.pelada', fn ($query) => $query->where('esporte_id', $perfil->esporte_id))
                            ->distinct('pelada_jogo_id')
                            ->count('pelada_jogo_id'),
                        'gols' => PeladaJogoParticipanteEstatistica::where('user_id', $user->id)
                            ->whereHas('jogo.pelada', fn ($query) => $query->where('esporte_id', $perfil->esporte_id))
                            ->sum('gols'),
                        'mvps' => PlayerVote::where('player_profile_id', $profile->id)->where('type', 'craque')->count(),
                        'assistencias' => PlayerVote::where('player_profile_id', $profile->id)->whereIn('type', ['garcom', 'maestro'])->count(),
                    ]
                );
            }

            if (PlayerVote::where('player_profile_id', $profile->id)->where('type', 'craque')->count() >= 3) {
                PlayerAchievement::firstOrCreate([
                    'player_profile_id' => $profile->id,
                    'key' => 'craque_3x',
                ], [
                    'title' => 'Craque 3x',
                    'description' => 'Recebeu pelo menos 3 votos de craque da rodada.',
                    'earned_at' => now(),
                ]);
            }

            PlayerRanking::updateOrCreate(
                ['player_profile_id' => $profile->id, 'period' => 'mensal', 'category' => 'reputacao'],
                [
                    'score' => (int) $profile->reputation_score,
                    'position' => null,
                    'starts_at' => now()->startOfMonth(),
                    'ends_at' => now()->endOfMonth(),
                ]
            );

            $user->refreshBadges();
        }

        PlayerRanking::where('period', 'mensal')
            ->where('category', 'reputacao')
            ->orderByDesc('score')
            ->get()
            ->values()
            ->each(fn (PlayerRanking $ranking, int $index) => $ranking->update(['position' => $index + 1]));
    }
}
