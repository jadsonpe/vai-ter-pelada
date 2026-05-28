<?php

namespace Database\Seeders;

use App\Models\AvaliacaoPartida;
use App\Models\Banner;
use App\Models\Esporte;
use App\Models\Notificacao;
use App\Models\Patrocinador;
use App\Models\Pelada;
use App\Models\PeladaCaixaMovimentacao;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
use App\Models\PlayerAchievement;
use App\Models\PlayerFollow;
use App\Models\PlayerProfile;
use App\Models\PlayerRanking;
use App\Models\PlayerSocialLink;
use App\Models\PlayerStat;
use App\Models\PlayerVote;
use App\Models\Presenca;
use App\Models\Report;
use App\Models\Sorteio;
use App\Models\SorteioSobra;
use App\Models\SorteioTime;
use App\Models\SorteioTimeJogador;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserEsportePerfil;
use App\Models\UserPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BrazilFullDemoSeeder extends Seeder
{
    private array $states = [
        ['uf' => 'AC', 'estado' => 'Acre', 'cidade' => 'Rio Branco', 'bairros' => ['Centro', 'Bosque', 'Estacao Experimental']],
        ['uf' => 'AL', 'estado' => 'Alagoas', 'cidade' => 'Maceio', 'bairros' => ['Ponta Verde', 'Jaragua', 'Farol']],
        ['uf' => 'AP', 'estado' => 'Amapa', 'cidade' => 'Macapa', 'bairros' => ['Central', 'Trem', 'Santa Rita']],
        ['uf' => 'AM', 'estado' => 'Amazonas', 'cidade' => 'Manaus', 'bairros' => ['Adrianopolis', 'Ponta Negra', 'Coroado']],
        ['uf' => 'BA', 'estado' => 'Bahia', 'cidade' => 'Salvador', 'bairros' => ['Barra', 'Pituba', 'Rio Vermelho']],
        ['uf' => 'CE', 'estado' => 'Ceara', 'cidade' => 'Fortaleza', 'bairros' => ['Aldeota', 'Meireles', 'Messejana']],
        ['uf' => 'DF', 'estado' => 'Distrito Federal', 'cidade' => 'Brasilia', 'bairros' => ['Asa Sul', 'Asa Norte', 'Taguatinga']],
        ['uf' => 'ES', 'estado' => 'Espirito Santo', 'cidade' => 'Vitoria', 'bairros' => ['Praia do Canto', 'Jardim Camburi', 'Centro']],
        ['uf' => 'GO', 'estado' => 'Goias', 'cidade' => 'Goiania', 'bairros' => ['Setor Bueno', 'Marista', 'Campinas']],
        ['uf' => 'MA', 'estado' => 'Maranhao', 'cidade' => 'Sao Luis', 'bairros' => ['Renascenca', 'Cohama', 'Calhau']],
        ['uf' => 'MT', 'estado' => 'Mato Grosso', 'cidade' => 'Cuiaba', 'bairros' => ['Goiabeiras', 'Duque de Caxias', 'Centro']],
        ['uf' => 'MS', 'estado' => 'Mato Grosso do Sul', 'cidade' => 'Campo Grande', 'bairros' => ['Centro', 'Jardim dos Estados', 'Tiradentes']],
        ['uf' => 'MG', 'estado' => 'Minas Gerais', 'cidade' => 'Belo Horizonte', 'bairros' => ['Savassi', 'Pampulha', 'Centro']],
        ['uf' => 'PA', 'estado' => 'Para', 'cidade' => 'Belem', 'bairros' => ['Nazare', 'Umarizal', 'Marco']],
        ['uf' => 'PB', 'estado' => 'Paraiba', 'cidade' => 'Joao Pessoa', 'bairros' => ['Tambau', 'Manaira', 'Bessa']],
        ['uf' => 'PR', 'estado' => 'Parana', 'cidade' => 'Curitiba', 'bairros' => ['Batel', 'Agua Verde', 'Centro']],
        ['uf' => 'PE', 'estado' => 'Pernambuco', 'cidade' => 'Recife', 'bairros' => ['Boa Viagem', 'Casa Forte', 'Derby']],
        ['uf' => 'PI', 'estado' => 'Piaui', 'cidade' => 'Teresina', 'bairros' => ['Jockey', 'Centro', 'Frei Serafim']],
        ['uf' => 'RJ', 'estado' => 'Rio de Janeiro', 'cidade' => 'Rio de Janeiro', 'bairros' => ['Tijuca', 'Copacabana', 'Barra da Tijuca']],
        ['uf' => 'RN', 'estado' => 'Rio Grande do Norte', 'cidade' => 'Natal', 'bairros' => ['Ponta Negra', 'Tirol', 'Alecrim']],
        ['uf' => 'RS', 'estado' => 'Rio Grande do Sul', 'cidade' => 'Porto Alegre', 'bairros' => ['Moinhos de Vento', 'Cidade Baixa', 'Menino Deus']],
        ['uf' => 'RO', 'estado' => 'Rondonia', 'cidade' => 'Porto Velho', 'bairros' => ['Centro', 'Embratel', 'Agenor de Carvalho']],
        ['uf' => 'RR', 'estado' => 'Roraima', 'cidade' => 'Boa Vista', 'bairros' => ['Centro', 'Cauame', 'Mecejana']],
        ['uf' => 'SC', 'estado' => 'Santa Catarina', 'cidade' => 'Florianopolis', 'bairros' => ['Trindade', 'Centro', 'Campeche']],
        ['uf' => 'SP', 'estado' => 'Sao Paulo', 'cidade' => 'Sao Paulo', 'bairros' => ['Vila Mariana', 'Santana', 'Tatuape']],
        ['uf' => 'SE', 'estado' => 'Sergipe', 'cidade' => 'Aracaju', 'bairros' => ['Atalaia', 'Jardins', 'Centro']],
        ['uf' => 'TO', 'estado' => 'Tocantins', 'cidade' => 'Palmas', 'bairros' => ['Plano Diretor Sul', 'Taquaralto', 'Centro']],
    ];

    private array $firstNames = ['Lucas', 'Matheus', 'Gabriel', 'Rafael', 'Bruno', 'Felipe', 'Pedro', 'Gustavo', 'Thiago', 'Joao', 'Mariana', 'Camila', 'Fernanda', 'Aline', 'Juliana', 'Larissa', 'Carolina', 'Beatriz', 'Renata', 'Patricia'];
    private array $lastNames = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Pereira', 'Costa', 'Almeida', 'Rodrigues', 'Ferreira', 'Lima', 'Gomes', 'Martins'];
    private array $positions = ['Goleiro', 'Zagueiro', 'Lateral', 'Volante', 'Meia', 'Atacante', 'Pivo', 'Armador', 'Ponteiro', 'Levantador', 'Ala', 'Fixo'];

    public function run(): void
    {
        if (
            User::count() >= 120
            && Pelada::count() >= 50
            && PlayerVote::count() >= 500
            && Report::count() >= 10
        ) {
            return;
        }

        $this->seedEsportes();
        $users = $this->seedUsers();
        $this->seedPlayerProfiles($users);
        $peladas = $this->seedPeladas($users);
        $jogos = $this->seedJogos($peladas);
        $this->seedReports($users, $peladas);
        $this->seedMarketing();
    }

    private function seedEsportes(): void
    {
        foreach (['Futebol', 'Futsal', 'Society', 'Volei', 'Basquete'] as $nome) {
            Esporte::updateOrCreate(
                ['slug' => Str::slug($nome)],
                ['nome' => $nome, 'ativo' => true]
            );
        }
    }

    private function seedUsers(): array
    {
        $users = ['organizers' => collect(), 'players' => collect(), 'blocked' => collect()];

        foreach ($this->states as $stateIndex => $state) {
            $organizer = User::updateOrCreate(
                ['email' => 'organizador.'.Str::lower($state['uf']).'@vaiterpelada.test'],
                [
                    'name' => 'Organizador '.$state['uf'],
                    'apelido' => 'Org '.$state['uf'],
                    'password' => Hash::make('asfdvaiterpelada11'),
                    'role' => 'organizador',
                    'status' => 'ativo',
                    'active' => true,
                    'plano' => $stateIndex % 3 === 0 ? 'pro' : 'free',
                    'limite_peladas' => 0,
                    'phone' => '55'.(80 + ($stateIndex % 19)).'9'.str_pad((string) (10000000 + $stateIndex), 8, '0', STR_PAD_LEFT),
                    'estado' => $state['uf'],
                    'cidade' => $state['cidade'],
                    'bairro' => $state['bairros'][0],
                    'data_nascimento' => now()->subYears(30 + ($stateIndex % 18))->subDays($stateIndex)->toDateString(),
                    'email_verified_at' => now(),
                ]
            );

            $users['organizers']->push($organizer);

            for ($i = 0; $i < 4; $i++) {
                $name = $this->fullName($stateIndex, $i);
                $blocked = $i === 3 && $stateIndex % 4 === 0;
                $player = User::updateOrCreate(
                    ['email' => 'jogador.'.Str::lower($state['uf']).'.'.$i.'@vaiterpelada.test'],
                    [
                        'name' => $name,
                        'apelido' => explode(' ', $name)[0].' '.$state['uf'],
                        'password' => Hash::make('asfdvaiterpelada11'),
                        'role' => 'jogador',
                        'status' => $blocked ? 'bloqueado' : 'ativo',
                        'active' => ! $blocked,
                        'phone' => '55'.(81 + (($stateIndex + $i) % 18)).'9'.str_pad((string) (20000000 + ($stateIndex * 10) + $i), 8, '0', STR_PAD_LEFT),
                        'estado' => $state['uf'],
                        'cidade' => $state['cidade'],
                        'bairro' => $state['bairros'][$i % count($state['bairros'])],
                        'data_nascimento' => now()->subYears(18 + (($stateIndex + $i) % 22))->subDays($i * 11)->toDateString(),
                        'email_verified_at' => now(),
                    ]
                );

                $users['players']->push($player);

                if ($blocked) {
                    $users['blocked']->push($player);
                }
            }
        }

        return $users;
    }

    private function seedPlayerProfiles(array $users): void
    {
        $esportes = Esporte::orderBy('id')->get();
        $themes = array_keys(PlayerProfile::gradientCoverOptions());
        $covers = array_keys(PlayerProfile::imageCoverOptions());

        $allUsers = $users['organizers']->merge($users['players']);

        foreach ($allUsers->values() as $index => $user) {
            $profile = PlayerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => PlayerProfile::uniqueSlug($user->apelido ?: $user->name ?: 'peladeiro'),
                    'nivel_label' => 'Novato',
                    'publico' => true,
                ]
            );
            $sport = $esportes[$index % $esportes->count()];
            $score = $user->status === 'bloqueado' ? 0 : 20 + ($index * 7) % 900;

            $profile->update([
                'slug' => PlayerProfile::uniqueSlug($user->apelido ?: $user->name, $profile->id),
                'esporte_principal_id' => $sport->id,
                'posicao_favorita' => $this->positions[$index % count($this->positions)],
                'nivel_label' => $score === 0 ? 'Novato' : $this->levelForScore($score),
                'reputation_score' => $score,
                'headline' => 'Bola no pe e resenha depois do jogo.',
                'bio' => 'Peladeiro de teste com perfil completo para validar ranking, avaliacoes, convites e denuncias.',
                'banner_theme' => $themes[$index % count($themes)],
                'banner_preset' => $index % 5 === 0 ? $covers[$index % count($covers)] : null,
                'publico' => true,
            ]);

            foreach ($esportes as $sportIndex => $esporte) {
                if (($index + $sportIndex) % 2 !== 0) {
                    continue;
                }

                UserEsportePerfil::updateOrCreate(
                    ['user_id' => $user->id, 'esporte_id' => $esporte->id],
                    ['posicao' => $this->positions[($index + $sportIndex) % count($this->positions)]]
                );
            }

            PlayerSocialLink::updateOrCreate(
                ['player_profile_id' => $profile->id, 'platform' => 'instagram'],
                ['url' => 'https://instagram.com/'.Str::slug($profile->slug, '')]
            );

            if ($index % 3 === 0) {
                PlayerSocialLink::updateOrCreate(
                    ['player_profile_id' => $profile->id, 'platform' => 'whatsapp'],
                    ['url' => 'https://wa.me/'.preg_replace('/\D+/', '', (string) $user->phone)]
                );
            }

            foreach ($esportes->take(3) as $sportIndex => $esporte) {
                $jogos = 4 + (($index + $sportIndex) % 30);
                $vitorias = min($jogos, (int) floor($jogos * (35 + (($index + $sportIndex) % 50)) / 100));

                PlayerStat::updateOrCreate(
                    ['player_profile_id' => $profile->id, 'esporte_id' => $esporte->id],
                    [
                        'jogos' => $jogos,
                        'vitorias' => $vitorias,
                        'gols' => ($index + 2) * ($sportIndex + 1) % 60,
                        'assistencias' => ($index + 5) * ($sportIndex + 2) % 45,
                        'mvps' => ($index + $sportIndex) % 8,
                        'sequencia_vitorias' => ($index + $sportIndex) % 6,
                        'aproveitamento' => $jogos ? round(($vitorias / $jogos) * 100, 2) : 0,
                    ]
                );
            }

            foreach ([
                'primeira_pelada' => 'Primeira pelada',
                'craque_3x' => 'Craque 3x',
                'resenha_limpa' => 'Resenha limpa',
            ] as $key => $title) {
                if (($index + strlen($key)) % 4 === 0) {
                    PlayerAchievement::updateOrCreate(
                        ['player_profile_id' => $profile->id, 'key' => $key],
                        ['title' => $title, 'description' => 'Conquista demo para testes.', 'earned_at' => now()->subDays($index % 30)]
                    );
                }
            }

            foreach (['semanal', 'mensal'] as $periodIndex => $period) {
                PlayerRanking::updateOrCreate(
                    ['player_profile_id' => $profile->id, 'period' => $period, 'category' => 'geral'],
                    [
                        'score' => $score + ($periodIndex * 15),
                        'position' => ($index % 50) + 1,
                        'starts_at' => now()->startOfMonth()->subWeeks($periodIndex)->toDateString(),
                        'ends_at' => now()->endOfMonth()->toDateString(),
                    ]
                );
            }

            UserPoint::firstOrCreate(
                ['user_id' => $user->id, 'origem' => 'demo_brasil', 'referencia' => 'perfil:'.$profile->id],
                ['valor' => $score, 'descricao' => 'Pontuacao inicial da carga Brasil.']
            );

            UserBadge::firstOrCreate(
                ['user_id' => $user->id, 'badge_key' => 'demo_brasil'],
                ['nome' => 'Brasil em campo', 'descricao' => 'Perfil criado pela carga completa de testes.', 'conquistado_em' => now()]
            );
        }

        $players = $users['players']->values();
        foreach ($players as $index => $player) {
            for ($i = 1; $i <= 3; $i++) {
                $followed = $players[($index + $i) % $players->count()];
                if ($player->id !== $followed->id) {
                    PlayerFollow::firstOrCreate(['follower_id' => $player->id, 'followed_id' => $followed->id]);
                }
            }
        }
    }

    private function seedPeladas(array $users): array
    {
        $esportes = Esporte::orderBy('id')->get();
        $peladas = collect();
        $players = $users['players']->values();
        $memberStatuses = ['ativo', 'pendente', 'bloqueado', 'saiu', 'inativo'];
        $solicitationStatuses = ['pendente', 'aprovada', 'recusada'];
        $solicitationTypes = ['entrar_pelada', 'virar_mensalista', 'convite_mensalista', 'convite_diarista'];

        foreach ($this->states as $stateIndex => $state) {
            $organizer = $users['organizers'][$stateIndex];

            for ($i = 0; $i < 2; $i++) {
                $sport = $esportes[($stateIndex + $i) % $esportes->count()];
                $slug = Str::slug('pelada '.$state['uf'].' '.$sport->slug.' '.$i);

                $pelada = Pelada::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'organizador_id' => $organizer->id,
                        'esporte_id' => $sport->id,
                        'nome' => 'Pelada '.$state['uf'].' '.($i + 1).' - '.$sport->nome,
                        'descricao' => 'Pelada completa para testar busca, inscricoes, caixa, rodadas, sorteio e avaliacoes em '.$state['cidade'].'.',
                        'data_fundacao' => now()->subMonths(6 + $stateIndex + $i)->toDateString(),
                        'categoria' => $i % 2 === 0 ? 'adulto' : 'infantil',
                        'cidade' => $state['cidade'],
                        'bairro' => $state['bairros'][$i % count($state['bairros'])],
                        'local_nome' => 'Arena '.$state['uf'].' '.($i + 1),
                        'endereco' => 'Rua das Peladas, '.(100 + $stateIndex + $i),
                        'local' => 'Arena '.$state['uf'].' '.($i + 1),
                        'dia_semana' => ($stateIndex + $i) % 7,
                        'horario' => sprintf('%02d:00', 18 + (($stateIndex + $i) % 4)),
                        'vagas_totais' => 12 + (($stateIndex + $i) % 14),
                        'vagas_diaristas' => 2 + (($stateIndex + $i) % 6),
                        'aceita_diarista' => true,
                        'requer_aprovacao' => $i % 2 === 0,
                        'capacidade' => 12 + (($stateIndex + $i) % 14),
                        'valor_mensalista' => 80 + (($stateIndex + $i) * 5),
                        'valor_diarista' => 15 + (($stateIndex + $i) % 8) * 5,
                        'status' => ['ativa', 'pausada', 'encerrada'][($stateIndex + $i) % 3],
                        'regras' => "1. Respeito acima de tudo.\n2. Pontualidade.\n3. Times definidos pela organizacao.",
                        'whatsapp_contato' => $organizer->phone,
                        'ativa' => ($stateIndex + $i) % 5 !== 0,
                    ]
                );

                $peladas->push($pelada);

                PeladaMembro::updateOrCreate(
                    ['pelada_id' => $pelada->id, 'user_id' => $organizer->id],
                    ['tipo' => 'mensalista', 'status' => 'ativo', 'prioridade' => 1, 'data_entrada' => now()->subMonths(5), 'mensalista_desde' => now()->subMonths(5)]
                );

                for ($m = 0; $m < 12; $m++) {
                    $player = $players[($stateIndex * 4 + $i * 7 + $m) % $players->count()];
                    PeladaMembro::updateOrCreate(
                        ['pelada_id' => $pelada->id, 'user_id' => $player->id],
                        [
                            'apelido' => $player->apelido,
                            'tipo' => $m % 3 === 0 ? 'mensalista' : 'diarista',
                            'status' => $memberStatuses[$m % count($memberStatuses)],
                            'prioridade' => $m + 1,
                            'data_entrada' => now()->subDays(120 - $m),
                            'observacao' => 'Membro demo '.$memberStatuses[$m % count($memberStatuses)],
                            'mensalista_desde' => $m % 3 === 0 ? now()->subMonths(2)->toDateString() : null,
                        ]
                    );
                }

                foreach ($solicitationTypes as $typeIndex => $type) {
                    $player = $players[($stateIndex + $i + $typeIndex + 20) % $players->count()];
                    PeladaSolicitacao::updateOrCreate(
                        ['pelada_id' => $pelada->id, 'user_id' => $player->id, 'tipo_solicitacao' => $type],
                        [
                            'tipo' => 'mensalista',
                            'status' => $solicitationStatuses[$typeIndex % count($solicitationStatuses)],
                            'mensagem' => 'Solicitacao demo: '.$type,
                            'respondido_por' => $typeIndex === 0 ? null : $organizer->id,
                            'respondido_em' => $typeIndex === 0 ? null : now()->subDays($typeIndex),
                            'avaliado_por' => $typeIndex === 0 ? null : $organizer->id,
                            'avaliado_em' => $typeIndex === 0 ? null : now()->subDays($typeIndex),
                        ]
                    );
                }
            }
        }

        return $peladas->all();
    }

    private function seedJogos(array $peladas): array
    {
        $jogos = collect();
        $gameStatuses = ['aberto', 'fechado', 'finalizado', 'cancelado', 'realizado'];
        $participantStatuses = ['confirmado', 'fila', 'cancelado'];
        $presenceStatuses = ['compareceu', 'faltou', 'justificou'];
        $voteTypes = ['craque', 'garcom', 'muralha', 'fair_play', 'carcara', 'fominha', 'maestro', 'xerife'];

        foreach ($peladas as $peladaIndex => $pelada) {
            $members = $pelada->membros()->with('user.playerProfile')->get()->values();

            for ($r = 1; $r <= 4; $r++) {
                $status = $gameStatuses[($peladaIndex + $r) % count($gameStatuses)];
                $date = $r <= 2
                    ? now()->subDays(($r * 7) + ($peladaIndex % 5))->setTime(19 + ($r % 3), 0)
                    : now()->addDays(($r * 7) + ($peladaIndex % 5))->setTime(19 + ($r % 3), 0);

                $jogo = PeladaJogo::updateOrCreate(
                    ['pelada_id' => $pelada->id, 'titulo' => 'Rodada '.$r],
                    [
                        'data_hora' => $date,
                        'data_jogo' => $date->toDateString(),
                        'horario' => $date->format('H:i:s'),
                        'capacidade' => min($pelada->vagas_totais ?: $pelada->capacidade, 18),
                        'vagas_totais' => min($pelada->vagas_totais ?: $pelada->capacidade, 18),
                        'vagas_diaristas' => min($pelada->vagas_diaristas ?: 4, 6),
                        'status' => $status,
                        'observacao' => 'Rodada demo '.$status,
                    ]
                );

                $jogos->push($jogo);

                foreach ($members->take(10) as $idx => $member) {
                    $participant = PeladaJogoParticipante::updateOrCreate(
                        ['pelada_jogo_id' => $jogo->id, 'user_id' => $member->user_id],
                        [
                            'pelada_membro_id' => $member->id,
                            'tipo' => $member->tipo,
                            'tipo_no_jogo' => $idx % 2 === 0 ? 'mensalista' : 'diarista',
                            'status' => $participantStatuses[$idx % count($participantStatuses)],
                            'ordem_chegada' => $idx + 1,
                            'posicao_fila' => $idx % 3 === 1 ? $idx : null,
                            'confirmado_em' => $idx % 3 !== 2 ? now()->subHours(4)->subMinutes($idx) : null,
                            'cancelado_em' => $idx % 3 === 2 ? now()->subHours(2) : null,
                            'presente_local' => $idx % 3 !== 2,
                            'ordem_presenca' => $idx % 3 !== 2 ? $idx + 1 : null,
                        ]
                    );

                    if ($idx < 8 && $participant->user_id) {
                        Presenca::updateOrCreate(
                            ['pelada_jogo_id' => $jogo->id, 'user_id' => $participant->user_id],
                            [
                                'status' => $presenceStatuses[$idx % count($presenceStatuses)],
                                'marcado_por' => $pelada->organizador_id,
                                'observacao' => 'Presenca demo: '.$presenceStatuses[$idx % count($presenceStatuses)],
                            ]
                        );
                    }
                }

                PeladaJogoParticipante::updateOrCreate(
                    ['pelada_jogo_id' => $jogo->id, 'nome_avulso' => 'Avulso '.$peladaIndex.'-'.$r],
                    [
                        'user_id' => null,
                        'pelada_membro_id' => null,
                        'tipo' => 'diarista',
                        'tipo_no_jogo' => 'avulso',
                        'status' => 'confirmado',
                        'ordem_chegada' => 99,
                        'presente_local' => true,
                        'ordem_presenca' => 99,
                    ]
                );

                $this->seedSorteioForGame($jogo);
                $this->seedCaixaForGame($jogo);

                $participants = $jogo->participantes()->whereNotNull('user_id')->where('status', 'confirmado')->get()->values();
                foreach ($participants->take(5) as $idx => $participant) {
                    $target = $participants->get(($idx + 1) % max(1, $participants->count()));
                    if (! $target || $target->user_id === $participant->user_id) {
                        continue;
                    }

                    AvaliacaoPartida::updateOrCreate(
                        ['pelada_jogo_id' => $jogo->id, 'avaliador_id' => $participant->user_id, 'avaliado_id' => $target->user_id],
                        ['estrelas' => 3 + ($idx % 3), 'comentario' => 'Avaliacao demo da rodada '.$jogo->titulo.'.']
                    );

                    $profile = $target->user
                        ? PlayerProfile::firstOrCreate(
                            ['user_id' => $target->user->id],
                            [
                                'slug' => PlayerProfile::uniqueSlug($target->user->apelido ?: $target->user->name ?: 'peladeiro'),
                                'nivel_label' => 'Novato',
                                'publico' => true,
                            ]
                        )
                        : null;
                    if ($profile) {
                        $type = $voteTypes[($peladaIndex + $r + $idx) % count($voteTypes)];
                        PlayerVote::firstOrCreate(
                            ['player_profile_id' => $profile->id, 'voter_id' => $participant->user_id, 'pelada_jogo_id' => $jogo->id, 'type' => $type],
                            ['metadata' => ['demo' => true, 'label' => $type]]
                        );
                    }
                }
            }
        }

        return $jogos->all();
    }

    private function seedSorteioForGame(PeladaJogo $jogo): void
    {
        $sorteio = Sorteio::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'criado_por' => $jogo->pelada->organizador_id],
            [
                'tipo_sorteio' => $jogo->id % 2 === 0 ? 'manual' : 'simples',
                'quantidade_times' => 2,
                'jogadores_por_time' => 5,
                'usar_ordem_manual' => $jogo->id % 2 === 0,
                'status' => in_array($jogo->status, ['realizado', 'finalizado'], true) ? 'publicado' : 'rascunho',
                'realizado_em' => now()->subHours(2),
            ]
        );

        $participants = $jogo->participantes()->whereNotNull('user_id')->where('status', 'confirmado')->get()->values();
        for ($teamNumber = 1; $teamNumber <= 2; $teamNumber++) {
            $time = SorteioTime::updateOrCreate(
                ['sorteio_id' => $sorteio->id, 'nome' => 'Time '.$teamNumber],
                ['nome_time' => 'Time '.$teamNumber, 'ordem' => $teamNumber]
            );

            foreach ($participants as $idx => $participant) {
                if ($idx % 2 !== $teamNumber - 1 || $idx >= 10) {
                    continue;
                }

                SorteioTimeJogador::updateOrCreate(
                    ['sorteio_time_id' => $time->id, 'user_id' => $participant->user_id],
                    ['pelada_jogo_participante_id' => $participant->id, 'ordem' => $idx + 1]
                );
            }
        }

        foreach ($participants->slice(10) as $idx => $participant) {
            SorteioSobra::updateOrCreate(
                ['sorteio_id' => $sorteio->id, 'user_id' => $participant->user_id],
                ['ordem' => $idx + 1]
            );
        }
    }

    private function seedCaixaForGame(PeladaJogo $jogo): void
    {
        $pelada = $jogo->pelada;
        $entries = [
            ['entrada', 'mensalidade', 'Mensalidades da rodada', 600],
            ['entrada', 'diarista', 'Diaristas confirmados', 150],
            ['saida', 'aluguel', 'Aluguel do campo ou quadra', 350],
            ['saida', 'material', 'Bolas, coletes e agua', 90],
        ];

        foreach ($entries as $entry) {
            PeladaCaixaMovimentacao::updateOrCreate(
                [
                    'pelada_id' => $pelada->id,
                    'pelada_jogo_id' => $jogo->id,
                    'tipo' => $entry[0],
                    'categoria' => $entry[1],
                ],
                [
                    'descricao' => $entry[2],
                    'valor' => $entry[3] + ($jogo->id % 20),
                    'data_pagamento' => $jogo->data_jogo ?: $jogo->data_hora->toDateString(),
                    'competencia' => ($jogo->data_jogo ?: $jogo->data_hora)->startOfMonth()->toDateString(),
                    'forma_pagamento' => ['PIX', 'Dinheiro', 'Cartao'][$jogo->id % 3],
                    'observacao' => 'Lancamento demo.',
                    'user_id' => $pelada->organizador_id,
                    'registrado_por' => $pelada->organizador_id,
                ]
            );
        }
    }

    private function seedReports(array $users, array $peladas): void
    {
        $admin = User::firstWhere('email', 'admin@admin.com') ?: User::where('role', 'admin')->first();
        $players = $users['players']->values();
        $statuses = [Report::STATUS_PENDING, Report::STATUS_REVIEWING, Report::STATUS_RESOLVED, Report::STATUS_REJECTED];

        foreach (Report::reasonsFor('pelada') as $reason => $label) {
            $idx = array_search($reason, array_keys(Report::reasonsFor('pelada')), true);
            $pelada = $peladas[$idx % count($peladas)];
            $reporter = $players[($idx + 5) % $players->count()];
            $status = $statuses[$idx % count($statuses)];

            Report::updateOrCreate(
                ['reporter_id' => $reporter->id, 'reportable_type' => Pelada::class, 'reportable_id' => $pelada->id, 'reason' => $reason],
                [
                    'description' => 'Denuncia demo de pelada: '.$label.'.',
                    'status' => $status,
                    'reviewed_by' => $status === Report::STATUS_PENDING ? null : $admin?->id,
                    'reviewed_at' => $status === Report::STATUS_PENDING ? null : now()->subDays(1),
                    'resolution' => $status === Report::STATUS_PENDING ? null : 'Analise demo registrada.',
                    'metadata' => ['type' => 'pelada', 'target_name' => $pelada->nome, 'target_url' => route('peladas.show', $pelada)],
                ]
            );
        }

        foreach (Report::reasonsFor('jogador') as $reason => $label) {
            $idx = array_search($reason, array_keys(Report::reasonsFor('jogador')), true);
            $target = $players[($idx + 12) % $players->count()];
            $reporter = $players[($idx + 2) % $players->count()];
            $profile = PlayerProfile::firstOrCreate(
                ['user_id' => $target->id],
                [
                    'slug' => PlayerProfile::uniqueSlug($target->apelido ?: $target->name ?: 'peladeiro'),
                    'nivel_label' => 'Novato',
                    'publico' => true,
                ]
            );
            $status = $statuses[($idx + 1) % count($statuses)];

            Report::updateOrCreate(
                ['reporter_id' => $reporter->id, 'reportable_type' => PlayerProfile::class, 'reportable_id' => $profile->id, 'reason' => $reason],
                [
                    'description' => 'Denuncia demo de jogador: '.$label.'.',
                    'status' => $status,
                    'reviewed_by' => $status === Report::STATUS_PENDING ? null : $admin?->id,
                    'reviewed_at' => $status === Report::STATUS_PENDING ? null : now()->subDays(2),
                    'resolution' => $status === Report::STATUS_PENDING ? null : 'Analise demo registrada.',
                    'metadata' => ['type' => 'jogador', 'target_name' => $target->name, 'target_url' => route('peladeiros.show', $profile)],
                ]
            );
        }

        foreach ($users['blocked'] as $blocked) {
            Notificacao::updateOrCreate(
                ['user_id' => $blocked->id, 'titulo' => 'Conta bloqueada para teste'],
                ['mensagem' => 'Este usuario foi bloqueado pela carga demo para validar o middleware.', 'link' => route('conta.bloqueada')]
            );
        }
    }

    private function seedMarketing(): void
    {
        foreach (['home', 'peladas', 'ranking'] as $index => $posicao) {
            Banner::updateOrCreate(
                ['titulo' => 'Banner Brasil '.$posicao],
                [
                    'imagem_url' => 'https://via.placeholder.com/1200x360?text=Vai+Ter+Pelada+'.$posicao,
                    'link_url' => '/peladas',
                    'imagem' => null,
                    'link' => '/peladas',
                    'posicao' => $posicao,
                    'ativo' => $index !== 2,
                    'data_inicio' => now()->subDays(10),
                    'data_fim' => now()->addDays(40),
                    'inicio_em' => now()->subDays(10),
                    'fim_em' => now()->addDays(40),
                ]
            );
        }

        foreach (['Arena Brasil', 'Bola Oficial', 'Agua da Resenha', 'Uniformes Pro'] as $index => $nome) {
            Patrocinador::updateOrCreate(
                ['nome' => $nome],
                [
                    'logo_url' => 'https://via.placeholder.com/240x120?text='.urlencode($nome),
                    'site_url' => 'https://vaiterpelada.com.br',
                    'logo' => null,
                    'link' => 'https://vaiterpelada.com.br',
                    'telefone' => '55819999'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                    'ativo' => $index !== 3,
                ]
            );
        }
    }

    private function fullName(int $stateIndex, int $offset): string
    {
        return $this->firstNames[($stateIndex + $offset) % count($this->firstNames)]
            .' '.
            $this->lastNames[($stateIndex * 2 + $offset) % count($this->lastNames)];
    }

    private function levelForScore(int $score): string
    {
        return match (true) {
            $score >= 700 => 'Dono da Bola',
            $score >= 500 => 'Rei da Quadra',
            $score >= 300 => 'Craque do Baba',
            $score >= 120 => 'Reserva de Luxo',
            default => 'Novato',
        };
    }
}
