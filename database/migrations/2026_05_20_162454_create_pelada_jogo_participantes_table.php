<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_jogo_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_jogo_id')->constrained('pelada_jogos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelada_membro_id')->nullable()->constrained('pelada_membros')->nullOnDelete();
            $table->enum('tipo', ['mensalista', 'diarista'])->default('diarista');
            $table->enum('status', ['confirmado', 'fila', 'cancelado'])->default('confirmado');
            $table->unsignedInteger('posicao_fila')->nullable();
            $table->timestamp('confirmado_em')->nullable();
            $table->timestamp('cancelado_em')->nullable();
            $table->timestamps();

            $table->unique(['pelada_jogo_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_jogo_participantes');
    }
};
