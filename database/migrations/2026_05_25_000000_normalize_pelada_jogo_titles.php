<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pelada_jogos')
            ->select('id', 'pelada_id')
            ->orderBy('pelada_id')
            ->orderBy('id')
            ->get()
            ->groupBy('pelada_id')
            ->each(function ($jogos) {
                foreach ($jogos->values() as $index => $jogo) {
                    DB::table('pelada_jogos')
                        ->where('id', $jogo->id)
                        ->update(['titulo' => 'Rodada '.($index + 1)]);
                }
            });
    }

    public function down(): void
    {
        // Nao ha reversao segura para titulos digitados manualmente antes desta normalizacao.
    }
};
