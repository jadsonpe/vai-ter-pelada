<?php

namespace Database\Seeders;

use App\Models\Pelada;
use App\Models\Torneio;
use App\Models\TorneioCartao;
use App\Models\TorneioGol;
use App\Models\TorneioJogo;
use App\Models\TorneioParticipante;
use App\Models\TorneioTime;
use App\Models\TorneioTimeJogador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TorneiosDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Torneio::where('slug', 'like', 'copa-demo-torneio-%')->get()->each->delete();

            $peladas = Pelada::query()
                ->with(['membros.user', 'esporte'])
                ->whereHas('esporte', fn ($query) => $query->whereIn('slug', ['futebol', 'society', 'futsal']))
                ->whereHas('membros', fn ($query) => $query->where('status', 'ativo'), '>=', 20)
                ->take(10)
                ->get();

            if ($peladas->isEmpty()) {
                return;
            }

            $nomes = [
                'Copa dos Mensalistas',
                'Torneio Rei da Quadra',
                'Desafio dos Amigos',
                'Copa Vai Ter Pelada',
                'Torneio da Resenha',
                'Copa Noturna',
                'Festival de Gols',
                'Taça dos Craques',
                'Copa da Galera',
                'Torneio Relâmpago',
            ];

            foreach ($peladas as $index => $pelada) {
                $slug = 'copa-demo-torneio-'.($index + 1);
                $torneio = Torneio::create([
                    'pelada_id' => $pelada->id,
                    'nome' => $nomes[$index] ?? 'Copa Demo '.($index + 1),
                    'slug' => $slug,
                    'data_torneio' => now()->subDays(20 - $index)->toDateString(),
                    'jogadores_por_time' => 5,
                    'quantidade_times' => 4,
                    'formato' => $index % 2 === 0 ? 'pontos_corridos' : 'mata_mata',
                    'tipo_confronto' => 'ida',
                    'quantidade_grupos' => 2,
                    'classificados_total' => 2,
                    'classificados_por_grupo' => 1,
                    'tipo_confronto_mata_mata' => 'unico',
                    'tipo_confronto_final' => 'unico',
                    'terceiro_lugar' => $index % 3 === 0,
                    'wo_gols_vencedor' => 3,
                    'wo_gols_perdedor' => 0,
                    'wo_conta_saldo' => true,
                    'status' => 'jogos_gerados',
                    'regras' => 'Seed de torneio com placares, gols e cartões para teste completo.',
                ]);

                $participantes = $this->criarParticipantes($torneio, $pelada->membros->where('status', 'ativo')->take(22)->values());
                $times = $this->criarTimes($torneio, $participantes->take(20)->values());
                $this->criarJogosComSumula($torneio, $times);
            }
        });
    }

    private function criarParticipantes(Torneio $torneio, Collection $membros): Collection
    {
        return $membros->map(function ($membro, int $index) use ($torneio) {
            return TorneioParticipante::create([
                'torneio_id' => $torneio->id,
                'user_id' => $membro->user_id,
                'pelada_membro_id' => $membro->id,
                'tipo' => $membro->tipo,
                'goleiro' => $index < 4,
                'cabeca_chave' => $index >= 4 && $index < 8,
                'status' => 'ativo',
            ]);
        });
    }

    private function criarTimes(Torneio $torneio, Collection $participantes): Collection
    {
        $times = collect(range(1, 4))->map(fn ($number) => TorneioTime::create([
            'torneio_id' => $torneio->id,
            'nome' => 'Time '.$number,
            'ordem' => $number,
        ]));

        foreach ($participantes as $index => $participante) {
            TorneioTimeJogador::create([
                'torneio_time_id' => $times[$index % 4]->id,
                'torneio_participante_id' => $participante->id,
                'ordem' => intdiv($index, 4) + 1,
            ]);
        }

        return $times->each(fn (TorneioTime $time) => $time->load('jogadores.participante'));
    }

    private function criarJogosComSumula(Torneio $torneio, Collection $times): void
    {
        $confrontos = [
            [0, 1, 2, 1],
            [2, 3, 1, 1],
            [0, 2, 3, 1],
            [1, 3, 0, 2],
            [0, 3, 1, 0],
            [1, 2, 2, 2],
        ];

        foreach ($confrontos as $ordem => [$a, $b, $golsA, $golsB]) {
            $jogo = TorneioJogo::create([
                'torneio_id' => $torneio->id,
                'time_a_id' => $times[$a]->id,
                'time_b_id' => $times[$b]->id,
                'fase' => $torneio->formato === 'mata_mata' && $ordem >= 4 ? 'semifinal' : 'pontos_corridos',
                'rodada' => intdiv($ordem, 2) + 1,
                'ordem' => $ordem + 1,
                'gols_a' => $golsA,
                'gols_b' => $golsB,
                'vencedor_id' => $golsA === $golsB ? null : ($golsA > $golsB ? $times[$a]->id : $times[$b]->id),
                'status' => 'finalizado',
                'observacao' => 'Partida criada pela seed de torneios.',
            ]);

            $this->criarGols($jogo, $times[$a], $golsA);
            $this->criarGols($jogo, $times[$b], $golsB);
            $this->criarCartoes($jogo, $times[$a], $times[$b], $ordem);
        }
    }

    private function criarGols(TorneioJogo $jogo, TorneioTime $time, int $quantidade): void
    {
        $jogadores = $time->jogadores->values();

        for ($i = 0; $i < $quantidade; $i++) {
            $participante = $jogadores[$i % max(1, $jogadores->count())]?->participante;

            if (! $participante) {
                continue;
            }

            TorneioGol::create([
                'torneio_jogo_id' => $jogo->id,
                'torneio_time_id' => $time->id,
                'torneio_participante_id' => $participante->id,
                'quantidade' => 1,
            ]);
        }
    }

    private function criarCartoes(TorneioJogo $jogo, TorneioTime $timeA, TorneioTime $timeB, int $ordem): void
    {
        foreach ([[$timeA, 'amarelo'], [$timeB, $ordem % 3 === 0 ? 'azul' : 'vermelho']] as [$time, $tipo]) {
            $participante = $time->jogadores->values()->get(($ordem + 1) % max(1, $time->jogadores->count()))?->participante;

            if (! $participante) {
                continue;
            }

            TorneioCartao::create([
                'torneio_jogo_id' => $jogo->id,
                'torneio_time_id' => $time->id,
                'torneio_participante_id' => $participante->id,
                'tipo' => $tipo,
                'quantidade' => 1,
            ]);
        }
    }
}
