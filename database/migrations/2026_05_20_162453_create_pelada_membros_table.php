<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelada_membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['mensalista', 'diarista'])->default('diarista');
            $table->enum('status', ['ativo', 'inativo', 'bloqueado'])->default('ativo');
            $table->date('mensalista_desde')->nullable();
            $table->timestamps();

            $table->unique(['pelada_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelada_membros');
    }
};
