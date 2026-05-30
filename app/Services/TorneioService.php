<?php

namespace App\Services;

use App\Models\Torneio;
use App\Models\TorneioGrupo;
use App\Models\TorneioJogo;
use App\Models\TorneioTime;
use Illuminate\Support\Collection;

class TorneioService
{
    public function classificacao(Torneio $torneio, ?TorneioGrupo $grupo = null): Collection
    {
        $times = $grupo
            ? $grupo->times
            : $torneio->times;

        $rows = $times->mapWithKeys(fn (TorneioTime $time) => [$time->id => [
            'time' => $time,
            'pontos' => 0,
            'jogos' => 0,
            'vitorias' => 0,
            'empates' => 0,
            'derrotas' => 0,
            'gols_pro' => 0,
            'gols_contra' => 0,
            'saldo' => 0,
            'amarelos' => 0,
            'vermelhos' => 0,
            'azuis' => 0,
        ]])->all();

        $jogos = $torneio->jogos()
            ->when($grupo, fn ($query) => $query->where('torneio_grupo_id', $grupo->id))
            ->where('status', 'finalizado')
            ->with('cartoes')
            ->get();

        foreach ($jogos as $jogo) {
            if (! $jogo->time_a_id || ! $jogo->time_b_id || $jogo->gols_a === null || $jogo->gols_b === null) {
                continue;
            }

            foreach ([[$jogo->time_a_id, $jogo->gols_a, $jogo->gols_b], [$jogo->time_b_id, $jogo->gols_b, $jogo->gols_a]] as [$timeId, $pro, $contra]) {
                if (! isset($rows[$timeId])) {
                    continue;
                }

                $rows[$timeId]['jogos']++;
                $rows[$timeId]['gols_pro'] += $pro;
                $rows[$timeId]['gols_contra'] += $contra;
                $rows[$timeId]['saldo'] = $rows[$timeId]['gols_pro'] - $rows[$timeId]['gols_contra'];
            }

            if ($jogo->gols_a > $jogo->gols_b) {
                $this->vitoria($rows, $jogo->time_a_id, $jogo->time_b_id);
            } elseif ($jogo->gols_b > $jogo->gols_a) {
                $this->vitoria($rows, $jogo->time_b_id, $jogo->time_a_id);
            } else {
                $rows[$jogo->time_a_id]['empates']++;
                $rows[$jogo->time_b_id]['empates']++;
                $rows[$jogo->time_a_id]['pontos']++;
                $rows[$jogo->time_b_id]['pontos']++;
            }

            foreach ($jogo->cartoes as $cartao) {
                if (! isset($rows[$cartao->torneio_time_id])) {
                    continue;
                }

                if ($cartao->tipo === 'vermelho') {
                    $rows[$cartao->torneio_time_id]['vermelhos'] += $cartao->quantidade;
                } elseif ($cartao->tipo === 'azul') {
                    $rows[$cartao->torneio_time_id]['azuis'] += $cartao->quantidade;
                } else {
                    $rows[$cartao->torneio_time_id]['amarelos'] += $cartao->quantidade;
                }
            }
        }

        return collect($rows)
            ->sortBy([
                ['pontos', 'desc'],
                ['vitorias', 'desc'],
                ['saldo', 'desc'],
                ['gols_pro', 'desc'],
                ['vermelhos', 'asc'],
                ['amarelos', 'asc'],
            ])
            ->values();
    }

    public function artilharia(Torneio $torneio): Collection
    {
        return $torneio->jogos()
            ->with('gols.participante', 'gols.time')
            ->where('wo', false)
            ->get()
            ->flatMap->gols
            ->groupBy('torneio_participante_id')
            ->map(function (Collection $gols) {
                $first = $gols->first();

                return [
                    'participante' => $first->participante,
                    'time' => $first->time,
                    'gols' => $gols->sum('quantidade'),
                    'jogos' => $gols->pluck('torneio_jogo_id')->unique()->count(),
                    'media' => $gols->pluck('torneio_jogo_id')->unique()->count()
                        ? round($gols->sum('quantidade') / $gols->pluck('torneio_jogo_id')->unique()->count(), 2)
                        : 0,
                ];
            })
            ->sortByDesc('gols')
            ->values();
    }

    public function disciplina(Torneio $torneio): Collection
    {
        return $torneio->jogos()
            ->with('cartoes.participante', 'cartoes.time')
            ->get()
            ->flatMap->cartoes
            ->groupBy('torneio_participante_id')
            ->map(function (Collection $cartoes) {
                $first = $cartoes->first();

                return [
                    'participante' => $first->participante,
                    'time' => $first->time,
                    'amarelos' => $cartoes->where('tipo', 'amarelo')->sum('quantidade'),
                    'vermelhos' => $cartoes->where('tipo', 'vermelho')->sum('quantidade'),
                    'azuis' => $cartoes->where('tipo', 'azul')->sum('quantidade'),
                    'jogos' => $cartoes->pluck('torneio_jogo_id')->unique()->count(),
                ];
            })
            ->sortByDesc(fn ($row) => ($row['vermelhos'] * 3) + ($row['azuis'] * 2) + $row['amarelos'])
            ->values();
    }

    private function vitoria(array &$rows, int $winnerId, int $loserId): void
    {
        $rows[$winnerId]['vitorias']++;
        $rows[$winnerId]['pontos'] += 3;
        $rows[$loserId]['derrotas']++;
    }
}
