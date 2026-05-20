<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorteios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_jogo_id')->constrained('pelada_jogos')->cascadeOnDelete();
            $table->foreignId('criado_por')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('quantidade_times')->default(2);
            $table->timestamp('realizado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteios');
    }
};
