<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelada_jogo_participantes', function (Blueprint $table) {
            $table->boolean('presente_local')->default(false)->after('ordem_chegada');
            $table->unsignedInteger('ordem_presenca')->nullable()->after('presente_local');
            $table->string('nome_avulso', 120)->nullable()->after('ordem_presenca');
        });

        Schema::table('pelada_jogo_participantes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('pelada_jogo_participantes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('sorteios', function (Blueprint $table) {
            $table->boolean('usar_ordem_manual')->default(false)->after('jogadores_por_time');
        });

        Schema::table('sorteio_time_jogadores', function (Blueprint $table) {
            $table->foreignId('pelada_jogo_participante_id')
                ->nullable()
                ->after('sorteio_time_id')
                ->constrained('pelada_jogo_participantes')
                ->nullOnDelete();
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sorteio_time_jogadores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelada_jogo_participante_id');
        });

        Schema::table('sorteios', function (Blueprint $table) {
            $table->dropColumn('usar_ordem_manual');
        });

        Schema::table('pelada_jogo_participantes', function (Blueprint $table) {
            $table->dropColumn(['presente_local', 'ordem_presenca', 'nome_avulso']);
        });
    }
};
