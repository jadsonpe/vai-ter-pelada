<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['mensalista'])->default('mensalista');
            $table->enum('status', ['pendente', 'aprovada', 'recusada'])->default('pendente');
            $table->text('mensagem')->nullable();
            $table->foreignId('avaliado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('avaliado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_solicitacoes');
    }
};
