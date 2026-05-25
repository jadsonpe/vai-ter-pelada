<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes_partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_jogo_id')->constrained('pelada_jogos')->cascadeOnDelete();
            $table->foreignId('avaliador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('avaliado_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('estrelas');
            $table->text('comentario')->nullable();
            $table->timestamps();
            $table->unique(['pelada_jogo_id', 'avaliador_id', 'avaliado_id'], 'avaliacoes_partidas_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_partidas');
    }
};
