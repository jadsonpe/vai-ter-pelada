<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $peladas = DB::table('peladas')->select('id', 'organizador_id')->get();

        foreach ($peladas as $pelada) {
            $membro = DB::table('pelada_membros')
                ->where('pelada_id', $pelada->id)
                ->where('user_id', $pelada->organizador_id)
                ->first();

            if ($membro) {
                DB::table('pelada_membros')
                    ->where('id', $membro->id)
                    ->update([
                        'tipo' => 'mensalista',
                        'status' => 'ativo',
                        'prioridade' => 100,
                        'mensalista_desde' => now()->toDateString(),
                        'observacao' => 'Organizador da pelada',
                        'updated_at' => now(),
                    ]);

                $membroId = $membro->id;
            } else {
                $membroId = DB::table('pelada_membros')->insertGetId([
                    'pelada_id' => $pelada->id,
                    'user_id' => $pelada->organizador_id,
                    'tipo' => 'mensalista',
                    'status' => 'ativo',
                    'prioridade' => 100,
                    'data_entrada' => now()->toDateString(),
                    'mensalista_desde' => now()->toDateString(),
                    'observacao' => 'Organizador da pelada',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('pelada_jogo_participantes')
                ->join('pelada_jogos', 'pelada_jogo_participantes.pelada_jogo_id', '=', 'pelada_jogos.id')
                ->where('pelada_jogos.pelada_id', $pelada->id)
                ->where('pelada_jogo_participantes.user_id', $pelada->organizador_id)
                ->update([
                    'pelada_jogo_participantes.pelada_membro_id' => $membroId,
                    'pelada_jogo_participantes.tipo' => 'mensalista',
                    'pelada_jogo_participantes.tipo_no_jogo' => 'mensalista',
                    'pelada_jogo_participantes.updated_at' => now(),
                ]);

            DB::table('pelada_solicitacoes')
                ->where('pelada_id', $pelada->id)
                ->where('user_id', $pelada->organizador_id)
                ->where('status', 'pendente')
                ->update([
                    'status' => 'aprovada',
                    'avaliado_por' => $pelada->organizador_id,
                    'avaliado_em' => now(),
                    'respondido_por' => $pelada->organizador_id,
                    'respondido_em' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};
