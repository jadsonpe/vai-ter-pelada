<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pelada_jogo_participante_estatisticas')) {
            return;
        }

        Schema::create('pelada_jogo_participante_estatisticas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelada_jogo_id');
            $table->unsignedBigInteger('pelada_jogo_participante_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedSmallInteger('gols')->default(0);
            $table->unsignedSmallInteger('cartoes_amarelos')->default(0);
            $table->unsignedSmallInteger('cartoes_vermelhos')->default(0);
            $table->unsignedSmallInteger('cartoes_azuis')->default(0);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique('pelada_jogo_participante_id', 'pelada_participante_estatistica_unique');
            $table->index(['pelada_jogo_id', 'user_id'], 'pjpe_jogo_user_idx');

            $table->foreign('pelada_jogo_id', 'pjpe_jogo_fk')
                ->references('id')
                ->on('pelada_jogos')
                ->cascadeOnDelete();
            $table->foreign('pelada_jogo_participante_id', 'pjpe_participante_fk')
                ->references('id')
                ->on('pelada_jogo_participantes')
                ->cascadeOnDelete();
            $table->foreign('user_id', 'pjpe_user_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_jogo_participante_estatisticas');
    }
};
