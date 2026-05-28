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
use App\Models\Presenca;
use App\Models\Sorteio;
use App\Models\SorteioTime;
use App\Models\SorteioTimeJogador;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() >= 100 && Pelada::count() >= 50 && PeladaJogo::count() >= 100) {
            return;
        }

        $this->seedUsers();
        $this->seedPeladas();
        $this->seedBanners();
        $this->seedSponsors();
        $this->seedNotifications();
    }

    protected function seedUsers(): void
    {
        $players = [
            ['name' => 'Rodrigo Alves', 'email' => 'rodrigo@vaiterpelada.test'],
            ['name' => 'Carla Mendes', 'email' => 'carla@vaiterpelada.test'],
            ['name' => 'Bruna Santos', 'email' => 'bruna@vaiterpelada.test'],
            ['name' => 'Lucas Oliveira', 'email' => 'lucas@vaiterpelada.test'],
            ['name' => 'Marcos Pereira', 'email' => 'marcos@vaiterpelada.test'],
            ['name' => 'Ana Rodrigues', 'email' => 'ana@vaiterpelada.test'],
            ['name' => 'Paulo Costa', 'email' => 'paulo@vaiterpelada.test'],
            ['name' => 'Tati Ferreira', 'email' => 'tati@vaiterpelada.test'],
            ['name' => 'Felipe Souza', 'email' => 'felipe@vaiterpelada.test'],
            ['name' => 'Renata Lima', 'email' => 'renata@vaiterpelada.test'],
            ['name' => 'Gabriel Martins', 'email' => 'gabriel@vaiterpelada.test'],
            ['name' => 'Beatriz Azevedo', 'email' => 'beatriz@vaiterpelada.test'],
            ['name' => 'Diego Ferreira', 'email' => 'diego@vaiterpelada.test'],
            ['name' => 'Natália Ramos', 'email' => 'natalia@vaiterpelada.test'],
            ['name' => 'Thiago Castro', 'email' => 'thiago@vaiterpelada.test'],
            ['name' => 'Marta Souza', 'email' => 'marta@vaiterpelada.test'],
            ['name' => 'Ricardo Lima', 'email' => 'ricardo@vaiterpelada.test'],
            ['name' => 'Fernanda Silva', 'email' => 'fernanda@vaiterpelada.test'],
        ];

        foreach ($players as $player) {
            User::firstOrCreate([
                'email' => $player['email'],
            ], [
                'name' => $player['name'],
                'password' => Hash::make('asfdvaiterpelada11'),
                'role' => 'jogador',
                'status' => 'ativo',
                'cidade' => 'São Paulo',
                'bairro' => 'Centro',
            ]);
        }
    }

    protected function seedPeladas(): void
    {
        $esportes = Esporte::pluck('id', 'slug');
        $organizador = User::firstWhere('email', 'organizador@vaiterpelada.test');
        $players = User::where('role', 'jogador')->orderBy('id')->get();

        $peladaData = [
            [
                'nome' => 'Pelada da Zona Norte',
                'slug' => 'pelada-zona-norte',
                'descricao' => 'Pelada de futebol society para quem curte rodada animada e troca de posições.',
                'local' => 'Campo do Leão',
                'cidade' => 'São Paulo',
                'bairro' => 'Santana',
                'dia_semana' => 3,
                'horario' => '20:00',
                'capacidade' => 22,
                'valor_mensalista' => 120,
                'valor_diarista' => 30,
                'esporte_slug' => 'futebol',
                'members' => [0, 1, 2, 3, 4, 5],
                'solicitacoes' => [6],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'mensalidade', 'descricao'=>'Mensalidade de março', 'valor'=>1200, 'data_pagamento'=>now()->subDays(5)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'aluguel', 'descricao'=>'Aluguel do campo', 'valor'=>350, 'data_pagamento'=>now()->subDays(4)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Futsal da Comunidade',
                'slug' => 'futsal-da-comunidade',
                'descricao' => 'Treinos e amistosos de futsal para todas as idades, com quadra coberta e estrutura.',
                'local' => 'Ginásio Vila Azul',
                'cidade' => 'São Paulo',
                'bairro' => 'Tatuapé',
                'dia_semana' => 2,
                'horario' => '19:00',
                'capacidade' => 12,
                'valor_mensalista' => 100,
                'valor_diarista' => 20,
                'esporte_slug' => 'futsal',
                'members' => [2, 3, 4, 5, 6],
                'solicitacoes' => [7],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'diarista', 'descricao'=>'Pagamentos de diaristas', 'valor'=>240, 'data_pagamento'=>now()->subDays(3)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'material', 'descricao'=>'Compra de bolas e coletes', 'valor'=>180, 'data_pagamento'=>now()->subDays(2)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Society da Morada',
                'slug' => 'society-da-morada',
                'descricao' => 'Boteco da bola com campo society, churrasco e roda de amigos após o jogo.',
                'local' => 'Clube Campineiro',
                'cidade' => 'Campinas',
                'bairro' => 'Barão Geraldo',
                'dia_semana' => 6,
                'horario' => '18:30',
                'capacidade' => 16,
                'valor_mensalista' => 140,
                'valor_diarista' => 35,
                'esporte_slug' => 'society',
                'members' => [1, 3, 5, 7, 8],
                'solicitacoes' => [0],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'mensalidade', 'descricao'=>'Mensalidade pago em dia', 'valor'=>700, 'data_pagamento'=>now()->subDays(7)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'refresco', 'descricao'=>'Água e isotônicos', 'valor'=>120, 'data_pagamento'=>now()->subDays(6)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Vôlei na Praia',
                'slug' => 'volei-na-praia',
                'descricao' => 'Quadra de areia com torneios semanais e muita gente disposta a sacar e bloquear.',
                'local' => 'Quadra Gonzaga',
                'cidade' => 'Santos',
                'bairro' => 'Gonzaga',
                'dia_semana' => 5,
                'horario' => '17:30',
                'capacidade' => 12,
                'valor_mensalista' => null,
                'valor_diarista' => 30,
                'esporte_slug' => 'volei',
                'members' => [4, 5, 6, 7, 9],
                'solicitacoes' => [8],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'diarista', 'descricao'=>'Pagamento da quadra', 'valor'=>180, 'data_pagamento'=>now()->subDays(4)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'bebidas', 'descricao'=>'Recompra de água', 'valor'=>40, 'data_pagamento'=>now()->subDays(3)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Basquete da Praça',
                'slug' => 'basquete-da-praca',
                'descricao' => 'Bola ao entardecer no parque, com treinos e partidas rápidas para amigos e novos jogadores.',
                'local' => 'Praça da Matriz',
                'cidade' => 'São Paulo',
                'bairro' => 'Itaim Bibi',
                'dia_semana' => 4,
                'horario' => '18:00',
                'capacidade' => 10,
                'valor_mensalista' => 90,
                'valor_diarista' => 25,
                'esporte_slug' => 'basquete',
                'members' => [0, 10, 11, 12, 13, 14],
                'solicitacoes' => [15],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'mensalidade', 'descricao'=>'Contribuição de abril', 'valor'=>540, 'data_pagamento'=>now()->subDays(8)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'manutenção', 'descricao'=>'Reparo das cestas', 'valor'=>120, 'data_pagamento'=>now()->subDays(6)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Treino Society do Clube',
                'slug' => 'treino-society-do-clube',
                'descricao' => 'Society noturno com churrasco depois do jogo e jogadas criativas no gramado sintético.',
                'local' => 'Clube Central',
                'cidade' => 'Campinas',
                'bairro' => 'Cambuí',
                'dia_semana' => 3,
                'horario' => '19:30',
                'capacidade' => 18,
                'valor_mensalista' => 130,
                'valor_diarista' => 35,
                'esporte_slug' => 'society',
                'members' => [1, 2, 8, 11, 13, 16],
                'solicitacoes' => [3, 5],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'mensalidade', 'descricao'=>'Mensalidade de abril', 'valor'=>780, 'data_pagamento'=>now()->subDays(10)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'aluguel', 'descricao'=>'Aluguel do gramado', 'valor'=>420, 'data_pagamento'=>now()->subDays(9)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
            [
                'nome' => 'Futsal do Bairro',
                'slug' => 'futsal-do-bairro',
                'descricao' => 'Futsal para companheiros de trabalho com espaço coberto e quadra pronta para decisões no último minuto.',
                'local' => 'Quadra Tatuapé',
                'cidade' => 'São Paulo',
                'bairro' => 'Tatuapé',
                'dia_semana' => 2,
                'horario' => '20:30',
                'capacidade' => 14,
                'valor_mensalista' => 95,
                'valor_diarista' => 25,
                'esporte_slug' => 'futsal',
                'members' => [2, 7, 9, 10, 14, 17],
                'solicitacoes' => [0, 4],
                'finance' => [
                    ['tipo'=>'entrada', 'categoria'=>'diarista', 'descricao'=>'Contribuições da semana', 'valor'=>225, 'data_pagamento'=>now()->subDays(5)->toDateString(), 'registrado_por'=>$organizador->id],
                    ['tipo'=>'saida', 'categoria'=>'material', 'descricao'=>'Compra de coletes', 'valor'=>80, 'data_pagamento'=>now()->subDays(2)->toDateString(), 'registrado_por'=>$organizador->id],
                ],
            ],
        ];

        foreach ($peladaData as $data) {
            $pelada = Pelada::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'organizador_id' => $organizador->id,
                    'esporte_id' => $esportes[$data['esporte_slug']] ?? $esportes->first(),
                    'nome' => $data['nome'],
                    'descricao' => $data['descricao'],
                    'data_fundacao' => now()->subMonths(rand(6, 36))->toDateString(),
                    'categoria' => $data['categoria'] ?? 'adulto',
                    'local' => $data['local'],
                    'cidade' => $data['cidade'],
                    'bairro' => $data['bairro'],
                    'dia_semana' => $data['dia_semana'],
                    'horario' => $data['horario'],
                    'capacidade' => $data['capacidade'],
                    'valor_mensalista' => $data['valor_mensalista'],
                    'valor_diarista' => $data['valor_diarista'],
                    'ativa' => true,
                ]
            );

            $this->seedPeladaMembers($pelada, $players, $data['members']);
            $this->seedPeladaGames($pelada, $players, $data['members']);
            $this->seedSolicitacoes($pelada, $players, $data['solicitacoes']);
            $this->seedFinance($pelada, $data['finance']);
        }
    }

    protected function seedPeladaMembers(Pelada $pelada, $players, array $indexMembers): void
    {
        $members = collect($indexMembers)
            ->map(fn ($index) => $players->get($index))
            ->filter()
            ->push($pelada->organizador)
            ->unique('id');

        foreach ($members as $member) {
            $pelada->membros()->firstOrCreate([
                'user_id' => $member->id,
            ], [
                'tipo' => $member->id === $pelada->organizador_id ? 'mensalista' : 'diarista',
                'status' => 'ativo',
                'mensalista_desde' => now()->subMonths(2)->toDateString(),
            ]);
        }
    }

    protected function seedPeladaGames(Pelada $pelada, $players, array $indexMembers): void
    {
        $members = collect($indexMembers)
            ->map(fn ($index) => $players->get($index))
            ->filter()
            ->push($pelada->organizador)
            ->unique('id');

        $gameDates = [
            now()->addDays(2)->setTime(20, 0),
            now()->addDays(9)->setTime(20, 0),
            now()->addDays(16)->setTime(20, 0),
        ];

        foreach ($gameDates as $index => $date) {
            $game = $pelada->jogos()->firstOrCreate(
                [
                    'titulo' => 'Rodada ' . ($index + 1),
                    'data_hora' => $date,
                ],
                [
                    'capacidade' => min($pelada->capacidade, 18),
                    'status' => 'aberto',
                ]
            );

            $this->seedParticipantes($game, $members);
            $this->seedSorteio($game);
            $this->seedPresencas($game, $members);
            $this->seedAvaliacoes($game, $members);
        }
    }

    protected function seedParticipantes(PeladaJogo $game, $members): void
    {
        $confirmed = $members->slice(0, 8);
        $waiting = $members->slice(8, 2);

        foreach ($confirmed as $member) {
            $membership = PeladaMembro::firstWhere([
                'pelada_id' => $game->pelada_id,
                'user_id' => $member->id,
            ]);

            PeladaJogoParticipante::firstOrCreate(
                [
                    'pelada_jogo_id' => $game->id,
                    'user_id' => $member->id,
                ], [
                    'pelada_membro_id' => $membership?->id,
                    'tipo' => 'mensalista',
                    'status' => 'confirmado',
                    'confirmado_em' => now()->subHour(),
                ]
            );
        }

        foreach ($waiting as $position => $member) {
            $membership = PeladaMembro::firstWhere([
                'pelada_id' => $game->pelada_id,
                'user_id' => $member->id,
            ]);

            PeladaJogoParticipante::firstOrCreate(
                [
                    'pelada_jogo_id' => $game->id,
                    'user_id' => $member->id,
                ], [
                    'pelada_membro_id' => $membership?->id,
                    'tipo' => 'diarista',
                    'status' => 'fila',
                    'posicao_fila' => $position + 1,
                ]
            );
        }
    }

    protected function seedSolicitacoes(Pelada $pelada, $players, array $indexSolicitacoes): void
    {
        foreach ($indexSolicitacoes as $index) {
            $user = $players->get($index);

            if (! $user) {
                continue;
            }

            PeladaSolicitacao::firstOrCreate(
                [
                    'pelada_id' => $pelada->id,
                    'user_id' => $user->id,
                    'tipo' => 'mensalista',
                ], [
                    'status' => 'pendente',
                    'mensagem' => 'Gostaria de participar das próximas partidas e me tornar membro.',
                ]
            );
        }
    }

    protected function seedFinance(Pelada $pelada, array $financas): void
    {
        foreach ($financas as $entry) {
            PeladaCaixaMovimentacao::firstOrCreate(
                [
                    'pelada_id' => $pelada->id,
                    'tipo' => $entry['tipo'],
                    'categoria' => $entry['categoria'],
                    'descricao' => $entry['descricao'],
                    'valor' => $entry['valor'],
                    'data_pagamento' => $entry['data_pagamento'],
                ], [
                    'pelada_jogo_id' => null,
                    'pelada_membro_id' => null,
                    'pelada_jogo_participante_id' => null,
                    'user_id' => $pelada->organizador_id,
                    'registrado_por' => $entry['registrado_por'],
                    'forma_pagamento' => 'PIX',
                ]
            );
        }
    }

    protected function seedSorteio(PeladaJogo $game): void
    {
        $creator = $game->pelada->organizador;
        $sorteio = Sorteio::firstOrCreate(
            [
                'pelada_jogo_id' => $game->id,
                'criado_por' => $creator->id,
            ], [
                'quantidade_times' => 2,
                'realizado_em' => now()->subHours(1),
            ]
        );

        $times = [
            ['nome' => 'Time Alfa', 'ordem' => 1],
            ['nome' => 'Time Beta', 'ordem' => 2],
        ];

        foreach ($times as $timeData) {
            $time = SorteioTime::firstOrCreate(
                [
                    'sorteio_id' => $sorteio->id,
                    'nome' => $timeData['nome'],
                ], [
                    'ordem' => $timeData['ordem'],
                ]
            );

            $participants = $game->participantes()->where('status', 'confirmado')->take(6)->get();

            foreach ($participants as $order => $participant) {
                if ($order % 2 !== ($timeData['ordem'] - 1)) {
                    continue;
                }

                SorteioTimeJogador::firstOrCreate(
                    [
                        'sorteio_time_id' => $time->id,
                        'user_id' => $participant->user_id,
                    ], [
                        'ordem' => $order + 1,
                    ]
                );
            }
        }
    }

    protected function seedPresencas(PeladaJogo $game, $members): void
    {
        $present = $members->take(8);
        $statuses = ['compareceu', 'faltou', 'justificou'];

        foreach ($present as $index => $member) {
            Presenca::firstOrCreate(
                [
                    'pelada_jogo_id' => $game->id,
                    'user_id' => $member->id,
                ], [
                    'status' => $statuses[$index % count($statuses)],
                    'marcado_por' => $game->pelada->organizador_id,
                    'observacao' => 'Presença registrada no local',
                ]
            );
        }
    }

    protected function seedAvaliacoes(PeladaJogo $game, $members): void
    {
        $participants = $game->participantes()->where('status', 'confirmado')->get();
        $avaliadores = $participants->slice(0, 4);
        $avaliados = $participants->slice(1, 5);

        foreach ($avaliadores as $index => $avaliador) {
            $avaliado = $avaliados->get($index);

            if (! $avaliado || $avaliador->user_id === $avaliado->user_id) {
                continue;
            }

            AvaliacaoPartida::firstOrCreate(
                [
                    'pelada_jogo_id' => $game->id,
                    'avaliador_id' => $avaliador->user_id,
                    'avaliado_id' => $avaliado->user_id,
                ], [
                    'estrelas' => 3 + ($index % 3),
                    'comentario' => 'Bom comportamento e chegou no horário.',
                ]
            );
        }
    }

    protected function seedBanners(): void
    {
        $banners = [
            [
                'titulo' => 'Junte a galera e marque sua pelada',
                'imagem_url' => 'https://via.placeholder.com/1200x300?text=Pelada+Imperd%C3%ADvel',
                'link_url' => '/peladas',
                'posicao' => 'home',
                'ativo' => true,
                'inicio_em' => now()->subDays(10)->toDateString(),
                'fim_em' => now()->addDays(30)->toDateString(),
            ],
            [
                'titulo' => 'Traga seu time e organize um torneio',
                'imagem_url' => 'https://via.placeholder.com/1200x300?text=Organize+um+Torneio',
                'link_url' => '/peladas',
                'posicao' => 'home',
                'ativo' => true,
                'inicio_em' => now()->subDays(5)->toDateString(),
                'fim_em' => now()->addDays(40)->toDateString(),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(
                ['titulo' => $banner['titulo']],
                $banner
            );
        }
    }

    protected function seedSponsors(): void
    {
        $sponsors = [
            ['nome' => 'Esporte Total', 'logo_url' => 'https://via.placeholder.com/200x100?text=Esporte+Total', 'site_url' => 'https://esportetotal.example.com'],
            ['nome' => 'Água Gelada', 'logo_url' => 'https://via.placeholder.com/200x100?text=%C3%81gua+Gelada', 'site_url' => 'https://aguagelada.example.com'],
            ['nome' => 'Troféu Club', 'logo_url' => 'https://via.placeholder.com/200x100?text=Trof%C3%A9u+Club', 'site_url' => 'https://trofeuclub.example.com'],
        ];

        foreach ($sponsors as $sponsor) {
            Patrocinador::firstOrCreate(
                ['nome' => $sponsor['nome']],
                array_merge($sponsor, ['ativo' => true])
            );
        }
    }

    protected function seedNotifications(): void
    {
        $organizador = User::firstWhere('email', 'organizador@vaiterpelada.test');
        $basicUser = User::where('role', 'jogador')->first();

        if ($organizador) {
            Notificacao::firstOrCreate([
                'user_id' => $organizador->id,
                'titulo' => 'Novo pedido de participação',
            ], [
                'mensagem' => 'Um jogador pediu para entrar em uma pelada. Verifique as solicitações no painel.',
                'link' => '/perfil/solicitacoes',
            ]);
        }

        if ($basicUser) {
            Notificacao::firstOrCreate([
                'user_id' => $basicUser->id,
                'titulo' => 'Bem-vindo ao Organiza Pelada',
            ], [
                'mensagem' => 'Confira as peladas próximas na sua região e confirme sua participação.',
                'link' => '/peladas',
            ]);
        }
    }
}
