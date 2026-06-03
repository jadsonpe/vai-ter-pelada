<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('player_votes')) {
            return;
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("
                DELETE pv1 FROM player_votes pv1
                INNER JOIN player_votes pv2
                    ON pv1.voter_id <=> pv2.voter_id
                    AND pv1.pelada_jogo_id <=> pv2.pelada_jogo_id
                    AND pv1.player_profile_id = pv2.player_profile_id
                    AND pv1.id < pv2.id
            ");
        }

        $hasUniqueIndex = $this->indexExists('player_votes', 'pv_unique_voter_jogo_profile');

        Schema::table('player_votes', function (Blueprint $table) use ($hasUniqueIndex) {
            if (! $hasUniqueIndex) {
                $table->unique(['voter_id', 'pelada_jogo_id', 'player_profile_id'], 'pv_unique_voter_jogo_profile');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('player_votes')) {
            return;
        }

        $hasUniqueIndex = $this->indexExists('player_votes', 'pv_unique_voter_jogo_profile');
        $hasOldIndex = $this->indexExists('player_votes', 'pv_voter_jogo_profile_idx');

        Schema::table('player_votes', function (Blueprint $table) use ($hasUniqueIndex, $hasOldIndex) {
            if ($hasUniqueIndex) {
                $table->dropUnique('pv_unique_voter_jogo_profile');
            }

            if (! $hasOldIndex) {
                $table->index(['voter_id', 'pelada_jogo_id', 'player_profile_id'], 'pv_voter_jogo_profile_idx');
            }
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return false;
        }

        return (bool) DB::selectOne(
            'SELECT 1 FROM information_schema.STATISTICS WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [DB::connection()->getDatabaseName(), $table, $name]
        );
    }
};
