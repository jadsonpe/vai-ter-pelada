<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_caixa_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->foreignId('pelada_jogo_id')->nullable()->constrained('pelada_jogos')->nullOnDelete();
            $table->foreignId('pelada_membro_id')->nullable()->constrained('pelada_membros')->nullOnDelete();
            $table->foreignId('pelada_jogo_participante_id')->nullable()->constrained('pelada_jogo_participantes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->string('categoria');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->date('competencia')->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['pelada_id', 'tipo', 'categoria']);
            $table->index(['pelada_id', 'competencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_caixa_movimentacoes');
    }
};
