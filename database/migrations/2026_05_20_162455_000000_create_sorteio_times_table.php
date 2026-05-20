<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorteio_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteio_id')->constrained('sorteios')->cascadeOnDelete();
            $table->string('nome');
            $table->unsignedTinyInteger('ordem')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteio_times');
    }
};
