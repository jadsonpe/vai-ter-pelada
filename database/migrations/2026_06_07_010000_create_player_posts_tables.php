<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_profile_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 20)->default('image');
            $table->string('categoria', 40)->default('momento');
            $table->string('legenda', 220)->nullable();
            $table->string('media_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('mime_type', 80)->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->default(0);
            $table->unsignedSmallInteger('duracao_segundos')->nullable();
            $table->string('status', 30)->default('publicado');
            $table->timestamp('publicado_em')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['player_profile_id', 'status', 'created_at'], 'player_posts_profile_status_created_idx');
        });

        Schema::create('player_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['player_post_id', 'user_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_post_likes');
        Schema::dropIfExists('player_posts');
    }
};
