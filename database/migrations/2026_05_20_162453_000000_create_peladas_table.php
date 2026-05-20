<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peladas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('esporte_id')->constrained('esportes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->string('local');
            $table->unsignedTinyInteger('dia_semana')->nullable();
            $table->time('horario')->nullable();
            $table->unsignedSmallInteger('capacidade')->default(20);
            $table->decimal('valor_mensalista', 10, 2)->nullable();
            $table->decimal('valor_diarista', 10, 2)->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peladas');
    }
};
