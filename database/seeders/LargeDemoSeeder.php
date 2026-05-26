<?php

namespace Database\Seeders;

use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaMembro;
use App\Models\PeladaJogoParticipante;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LargeDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure basic esportes exist
        $names = ['Futebol', 'Futsal', 'Society', 'Volei', 'Basquete'];
        foreach ($names as $nome) {
            Esporte::firstOrCreate([
                'slug' => Str::slug($nome),
            ], [
                'nome' => $nome,
                'ativo' => true,
            ]);
        }

        $esportes = Esporte::where('ativo', true)->pluck('id')->toArray();

        // Create an organizer if missing
        $organizador = User::firstOrCreate([
            'email' => 'organizador@vaiterpelada.test',
        ], [
            'name' => 'Organizador Demo',
            'password' => bcrypt('password'),
            'role' => 'organizador',
            'status' => 'ativo',
            'cidade' => 'São Paulo',
            'bairro' => 'Centro',
        ]);

        // Create 30 jogadores
        $players = User::factory()->count(30)->create()->each(function ($u) {
            $u->role = 'jogador';
            $u->status = 'ativo';
            $u->cidade = ['São Paulo','Campinas','Santos'][array_rand([0,1,2])];
            $u->bairro = 'Demo Bairro '.rand(1,20);
            $u->save();
        });

        $playersArr = $players->values();

        // Create 30 peladas
        $cities = ['São Paulo','Campinas','Santos','Guarulhos','Osasco'];
        for ($i = 1; $i <= 30; $i++) {
            $esporteId = $esportes[array_rand($esportes)];
            $cidade = $cities[array_rand($cities)];
            $bairro = 'Bairro Demo '.rand(1,30);

            $pelada = Pelada::firstOrCreate([
                'slug' => Str::slug("pelada-demo-$i"),
            ], [
                'organizador_id' => $organizador->id,
                'esporte_id' => $esporteId,
                'nome' => "Pelada Demo $i",
                'data_fundacao' => now()->subMonths(rand(3, 48))->toDateString(),
                'categoria' => rand(1, 5) === 1 ? 'infantil' : 'adulto',
                'descricao' => 'Pelada de demonstração para testes automatizados e manuais.',
                'local' => "Quadra Demo $i",
                'cidade' => $cidade,
                'bairro' => $bairro,
                'dia_semana' => rand(0,6),
                'horario' => sprintf('%02d:00', rand(17,21)),
                'capacidade' => rand(10,30),
                'valor_mensalista' => rand(50,200),
                'valor_diarista' => rand(10,60),
                'ativa' => true,
                'status' => 'ativa',
            ]);

            // Add random members (8-20)
            $countMembers = rand(8,20);
            $sample = $playersArr->random(min($countMembers, $playersArr->count()));
            foreach ($sample as $player) {
                PeladaMembro::firstOrCreate([
                    'pelada_id' => $pelada->id,
                    'user_id' => $player->id,
                ], [
                    'tipo' => rand(0,1) ? 'mensalista' : 'diarista',
                    'status' => 'ativo',
                ]);
            }

            // Ensure organizer is a member
            PeladaMembro::firstOrCreate([
                'pelada_id' => $pelada->id,
                'user_id' => $organizador->id,
            ], [
                'tipo' => 'mensalista',
                'status' => 'ativo',
            ]);

            // Create 3 rodadas
            for ($r = 1; $r <= 3; $r++) {
                $date = now()->addDays($r * 7)->setTime(rand(17,21), 0);
                $game = PeladaJogo::create([
                    'pelada_id' => $pelada->id,
                    'titulo' => 'Rodada '.$r,
                    'data_hora' => $date,
                    'capacidade' => min($pelada->capacidade, 18),
                    'status' => 'aberto',
                ]);

                // Participants: take up to capacity
                $members = PeladaMembro::where('pelada_id', $pelada->id)->inRandomOrder()->limit($game->capacidade)->get();
                foreach ($members as $idx => $m) {
                    PeladaJogoParticipante::firstOrCreate([
                        'pelada_jogo_id' => $game->id,
                        'user_id' => $m->user_id,
                    ], [
                        'pelada_membro_id' => $m->id,
                        'tipo' => $m->tipo,
                        'status' => $idx < ($game->capacidade - 2) ? 'confirmado' : 'fila',
                        'confirmado_em' => now()->subMinutes(rand(10,100)),
                        'posicao_fila' => $idx >= ($game->capacidade - 2) ? ($idx - ($game->capacidade - 2) + 1) : null,
                    ]);
                }
            }
        }
    }
}
