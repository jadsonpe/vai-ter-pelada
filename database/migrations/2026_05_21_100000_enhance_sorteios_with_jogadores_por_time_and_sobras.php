<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sorteios', function (Blueprint $table) {
            $table->unsignedTinyInteger('jogadores_por_time')->default(5)->after('quantidade_times');
        });

        Schema::create('sorteio_sobras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteio_id')->constrained('sorteios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('ordem')->default(1);
            $table->timestamps();

            $table->unique(['sorteio_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteio_sobras');

        Schema::table('sorteios', function (Blueprint $table) {
            $table->dropColumn('jogadores_por_time');
        });
    }
};
