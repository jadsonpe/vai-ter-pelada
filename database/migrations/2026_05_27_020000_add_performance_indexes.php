<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndex('pelada_jogo_participantes', ['user_id', 'presente_local'], 'pjp_user_presente_idx');
        $this->addIndex('pelada_jogo_participantes', ['pelada_jogo_id', 'status'], 'pjp_jogo_status_idx');
        $this->addIndex('pelada_jogo_participantes', ['pelada_jogo_id', 'user_id', 'presente_local'], 'pjp_jogo_user_presente_idx');

        $this->addIndex('player_votes', ['pelada_jogo_id', 'type', 'player_profile_id'], 'pv_jogo_type_profile_idx');
        $this->addIndex('player_votes', ['voter_id', 'pelada_jogo_id', 'player_profile_id'], 'pv_voter_jogo_profile_idx');
        $this->addIndex('player_votes', ['player_profile_id', 'type'], 'pv_profile_type_idx');

        $this->addIndex('pelada_membros', ['pelada_id', 'status'], 'pm_pelada_status_idx');
        $this->addIndex('pelada_membros', ['pelada_id', 'user_id', 'status'], 'pm_pelada_user_status_idx');

        $this->addIndex('pelada_jogos', ['status', 'data_hora'], 'pj_status_data_idx');
        $this->addIndex('pelada_jogos', ['pelada_id', 'status', 'data_hora'], 'pj_pelada_status_data_idx');

        $this->addIndex('notificacoes', ['user_id', 'lida_em'], 'notificacoes_user_lida_idx');
        $this->addIndex('peladas', ['ativa', 'status', 'esporte_id', 'categoria'], 'peladas_public_filters_idx');
    }

    public function down(): void
    {
        $this->dropIndex('pelada_jogo_participantes', 'pjp_user_presente_idx');
        $this->dropIndex('pelada_jogo_participantes', 'pjp_jogo_status_idx');
        $this->dropIndex('pelada_jogo_participantes', 'pjp_jogo_user_presente_idx');

        $this->dropIndex('player_votes', 'pv_jogo_type_profile_idx');
        $this->dropIndex('player_votes', 'pv_voter_jogo_profile_idx');
        $this->dropIndex('player_votes', 'pv_profile_type_idx');

        $this->dropIndex('pelada_membros', 'pm_pelada_status_idx');
        $this->dropIndex('pelada_membros', 'pm_pelada_user_status_idx');

        $this->dropIndex('pelada_jogos', 'pj_status_data_idx');
        $this->dropIndex('pelada_jogos', 'pj_pelada_status_data_idx');

        $this->dropIndex('notificacoes', 'notificacoes_user_lida_idx');
        $this->dropIndex('peladas', 'peladas_public_filters_idx');
    }

    private function addIndex(string $table, array $columns, string $name): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($columns, $name));
    }

    private function dropIndex(string $table, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
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
