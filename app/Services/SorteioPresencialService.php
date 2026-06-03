<?php

namespace App\Services;

use App\Models\PeladaJogoParticipante;
use Illuminate\Support\Collection;

class SorteioPresencialService
{
    /**
     * @param  Collection<int, PeladaJogoParticipante>  $presentes
     * @return array{times: array<int, array{nome: string, ordem: int, participantes: Collection<int, PeladaJogoParticipante>}>}
     */
    public function montarTimes(Collection $presentes, int $jogadoresPorTime, ?int $quantidadeTimes = null): array
    {
        $jogadoresPorTime = max(1, $jogadoresPorTime);
        $quantidadeTimes = $quantidadeTimes ? max(1, $quantidadeTimes) : null;

        if ($quantidadeTimes) {
            $letras = range('A', 'Z');

            if ($quantidadeTimes === 1) {
                return [
                    'times' => [[
                        'nome' => 'Time A',
                        'ordem' => 1,
                        'participantes' => $presentes->take($jogadoresPorTime)->values(),
                    ]],
                ];
            }

            $primeiroBloco = $presentes->take($jogadoresPorTime * 2)->shuffle()->values();
            [$timeA, $timeB] = $this->distribuirEntreDoisTimes($primeiroBloco, $jogadoresPorTime);

            $times = [
                [
                    'nome' => 'Time A',
                    'ordem' => 1,
                    'participantes' => $timeA,
                ],
                [
                    'nome' => 'Time B',
                    'ordem' => 2,
                    'participantes' => $timeB,
                ],
            ];

            foreach ($presentes->skip($jogadoresPorTime * 2)->chunk($jogadoresPorTime)->take($quantidadeTimes - 2)->values() as $index => $chunk) {
                $ordem = $index + 3;
                $letra = $letras[$ordem - 1] ?? (string) $ordem;

                $times[] = [
                    'nome' => 'Time '.$letra,
                    'ordem' => $ordem,
                    'participantes' => $chunk->values(),
                ];
            }

            return ['times' => $times];
        }

        $vagasIniciais = $jogadoresPorTime * 2;
        $primeiroBloco = $presentes->take($vagasIniciais)->shuffle()->values();
        $restantes = $presentes->skip($vagasIniciais)->values();

        $times = [];
        $times[] = [
            'nome' => 'Time A',
            'ordem' => 1,
            'participantes' => $this->distribuirEntreDoisTimes($primeiroBloco, $jogadoresPorTime)[0],
        ];
        $times[] = [
            'nome' => 'Time B',
            'ordem' => 2,
            'participantes' => $this->distribuirEntreDoisTimes($primeiroBloco, $jogadoresPorTime)[1],
        ];

        $letras = range('C', 'Z');
        $indiceLetra = 0;

        foreach ($restantes->chunk($jogadoresPorTime) as $chunk) {
            $letra = $letras[$indiceLetra] ?? (string) ($indiceLetra + 3);
            $times[] = [
                'nome' => 'Time '.$letra,
                'ordem' => count($times) + 1,
                'participantes' => $chunk->values(),
            ];
            $indiceLetra++;
        }

        return ['times' => $times];
    }

    /**
     * @return array{0: Collection<int, PeladaJogoParticipante>, 1: Collection<int, PeladaJogoParticipante>}
     */
    private function distribuirEntreDoisTimes(Collection $jogadores, int $limitePorTime): array
    {
        $timeA = collect();
        $timeB = collect();

        foreach ($jogadores as $participante) {
            if ($timeA->count() < $limitePorTime && ($timeB->count() >= $limitePorTime || $timeA->count() <= $timeB->count())) {
                $timeA->push($participante);
            } elseif ($timeB->count() < $limitePorTime) {
                $timeB->push($participante);
            } elseif ($timeA->count() < $limitePorTime) {
                $timeA->push($participante);
            }
        }

        return [$timeA, $timeB];
    }

    /**
     * @param  Collection<int, PeladaJogoParticipante>  $confirmados
     * @return Collection<int, PeladaJogoParticipante>
     */
    public function ordenarPresentes(Collection $confirmados, bool $usarOrdemManual): Collection
    {
        $presentes = $confirmados
            ->where('presente_local', true)
            ->values();

        if ($usarOrdemManual) {
            return $presentes
                ->sortBy(fn (PeladaJogoParticipante $p) => $p->ordem_presenca ?? PHP_INT_MAX)
                ->values();
        }

        $mensalistas = $presentes
            ->filter(fn (PeladaJogoParticipante $p) => $p->tipo === 'mensalista' || $p->tipo_no_jogo === 'mensalista')
            ->sortBy(fn (PeladaJogoParticipante $p) => $p->ordem_presenca ?? $p->ordem_chegada ?? PHP_INT_MAX)
            ->values();

        $diaristas = $presentes
            ->reject(fn (PeladaJogoParticipante $p) => $mensalistas->contains('id', $p->id))
            ->sortBy(fn (PeladaJogoParticipante $p) => $p->ordem_presenca ?? $p->ordem_chegada ?? PHP_INT_MAX)
            ->values();

        return $mensalistas->concat($diaristas)->values();
    }
}
