<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_jogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->string('titulo');
            $table->dateTime('data_hora');
            $table->unsignedSmallInteger('capacidade')->nullable();
            $table->enum('status', ['aberto', 'fechado', 'realizado', 'cancelado'])->default('aberto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_jogos');
    }
};
