<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('imagem_url')->nullable();
            $table->string('link_url')->nullable();
            $table->string('posicao')->default('home');
            $table->boolean('ativo')->default(true);
            $table->date('inicio_em')->nullable();
            $table->date('fim_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
