<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('phone');
            }
        });

        Schema::create('user_esporte_perfis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('esporte_id')->constrained('esportes')->cascadeOnDelete();
            $table->string('posicao')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'esporte_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_esporte_perfis');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'data_nascimento')) {
                $table->dropColumn('data_nascimento');
            }
        });
    }
};
