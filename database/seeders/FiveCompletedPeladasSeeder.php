<?php

namespace Database\Seeders;

use App\Models\AvaliacaoPartida;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\Presenca;
use Illuminate\Database\Seeder;

class FiveCompletedPeladasSeeder extends Seeder
{
    public function run(): void
    {
        if (
            PeladaJogo::where('titulo', 'Rodada efetuada demo')->count() >= 5
            && AvaliacaoPartida::count() >= 1000
        ) {
            return;
        }

        Pelada::query()
            ->with(['membros.user'])
            ->whereHas('membros')
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->each(function (Pelada $pelada, int $index) {
                $pelada->update([
                    'status' => 'ativa',
                    'ativa' => true,
                ]);

                $jogo = PeladaJogo::updateOrCreate(
                    [
                        'pelada_id' => $pelada->id,
                        'titulo' => 'Rodada efetuada demo',
                    ],
                    [
                        'data_hora' => now()->subDays($index + 1)->setTime(20, 0),
                        'data_jogo' => now()->subDays($index + 1)->toDateString(),
                        'horario' => '20:00:00',
                        'capacidade' => 30,
                        'vagas_totais' => 30,
                        'vagas_diaristas' => 0,
                        'status' => 'realizado',
                        'observacao' => 'Rodada efetuada para teste com presencas e avaliacoes.',
                    ]
                );

                $members = $pelada->membros()
                    ->with('user')
                    ->whereNotNull('user_id')
                    ->where('status', 'ativo')
                    ->orderBy('prioridade')
                    ->limit(30)
                    ->get()
                    ->values();

                foreach ($members as $position => $member) {
                    PeladaJogoParticipante::updateOrCreate(
                        [
                            'pelada_jogo_id' => $jogo->id,
                            'user_id' => $member->user_id,
                        ],
                        [
                            'pelada_membro_id' => $member->id,
                            'tipo' => 'mensalista',
                            'tipo_no_jogo' => 'mensalista',
                            'status' => 'confirmado',
                            'ordem_chegada' => $position + 1,
                            'posicao_fila' => null,
                            'confirmado_em' => now()->subDays($index + 1)->setTime(18, 30)->addMinutes($position),
                            'cancelado_em' => null,
                            'presente_local' => true,
                            'ordem_presenca' => $position + 1,
                        ]
                    );

                    Presenca::updateOrCreate(
                        [
                            'pelada_jogo_id' => $jogo->id,
                            'user_id' => $member->user_id,
                        ],
                        [
                            'status' => 'compareceu',
                            'marcado_por' => $pelada->organizador_id,
                            'observacao' => 'Presenca confirmada na rodada efetuada demo.',
                        ]
                    );
                }

                $participants = $jogo->participantes()
                    ->whereNotNull('user_id')
                    ->where('status', 'confirmado')
                    ->orderBy('ordem_presenca')
                    ->get()
                    ->values();

                AvaliacaoPartida::where('pelada_jogo_id', $jogo->id)->delete();

                foreach ($participants as $position => $participant) {
                    if ($position >= 20) {
                        continue;
                    }

                    $targets = $position < 10
                        ? $participants->filter(fn ($target) => $target->user_id !== $participant->user_id)
                        : $participants
                            ->filter(fn ($target) => $target->user_id !== $participant->user_id)
                            ->take(5);

                    foreach ($targets as $targetIndex => $target) {
                        if (! $target || $target->user_id === $participant->user_id) {
                            continue;
                        }

                        AvaliacaoPartida::updateOrCreate(
                            [
                                'pelada_jogo_id' => $jogo->id,
                                'avaliador_id' => $participant->user_id,
                                'avaliado_id' => $target->user_id,
                            ],
                            [
                                'estrelas' => 3 + (($position + $targetIndex) % 3),
                                'comentario' => 'Avaliacao demo apos pelada efetuada.',
                            ]
                        );
                    }
                }
            });
    }
}
