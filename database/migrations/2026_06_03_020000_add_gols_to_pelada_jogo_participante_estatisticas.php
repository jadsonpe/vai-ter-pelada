<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pelada_jogo_participante_estatisticas')
            || Schema::hasColumn('pelada_jogo_participante_estatisticas', 'gols')) {
            return;
        }

        Schema::table('pelada_jogo_participante_estatisticas', function (Blueprint $table) {
            $table->unsignedSmallInteger('gols')->default(0)->after('user_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pelada_jogo_participante_estatisticas')
            || ! Schema::hasColumn('pelada_jogo_participante_estatisticas', 'gols')) {
            return;
        }

        Schema::table('pelada_jogo_participante_estatisticas', function (Blueprint $table) {
            $table->dropColumn('gols');
        });
    }
};
