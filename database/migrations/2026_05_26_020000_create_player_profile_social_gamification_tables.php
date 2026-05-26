<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->foreignId('esporte_principal_id')->nullable()->constrained('esportes')->nullOnDelete();
            $table->string('posicao_favorita')->nullable();
            $table->string('nivel_label')->default('Perna de Pau');
            $table->unsignedInteger('reputation_score')->default(0);
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('banner_path')->nullable();
            $table->boolean('publico')->default(true);
            $table->timestamps();
        });

        Schema::create('player_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained('player_profiles')->cascadeOnDelete();
            $table->string('platform', 30);
            $table->string('url');
            $table->timestamps();

            $table->unique(['player_profile_id', 'platform']);
        });

        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained('player_profiles')->cascadeOnDelete();
            $table->foreignId('esporte_id')->nullable()->constrained('esportes')->nullOnDelete();
            $table->unsignedInteger('jogos')->default(0);
            $table->unsignedInteger('vitorias')->default(0);
            $table->unsignedInteger('gols')->default(0);
            $table->unsignedInteger('assistencias')->default(0);
            $table->unsignedInteger('mvps')->default(0);
            $table->unsignedInteger('sequencia_vitorias')->default(0);
            $table->decimal('aproveitamento', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['player_profile_id', 'esporte_id']);
        });

        Schema::create('player_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained('player_profiles')->cascadeOnDelete();
            $table->foreignId('voter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pelada_jogo_id')->nullable()->constrained('pelada_jogos')->cascadeOnDelete();
            $table->string('type', 40);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });

        Schema::create('player_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained('player_profiles')->cascadeOnDelete();
            $table->string('key');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('earned_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['player_profile_id', 'key']);
        });

        Schema::create('player_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')->constrained('player_profiles')->cascadeOnDelete();
            $table->string('period', 20);
            $table->string('category', 40);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('position')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();

            $table->index(['period', 'category', 'score']);
        });

        $usedSlugs = [];
        DB::table('users')->select('id', 'name', 'apelido')->orderBy('id')->chunkById(100, function ($users) use (&$usedSlugs) {
            foreach ($users as $user) {
                $base = Str::slug($user->apelido ?: $user->name ?: 'peladeiro') ?: 'peladeiro';
                $slug = $base;
                $count = 2;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = "{$base}-{$count}";
                    $count++;
                }

                $usedSlugs[] = $slug;

                DB::table('player_profiles')->insert([
                    'user_id' => $user->id,
                    'slug' => $slug,
                    'nivel_label' => 'Perna de Pau',
                    'reputation_score' => 0,
                    'publico' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_rankings');
        Schema::dropIfExists('player_achievements');
        Schema::dropIfExists('player_votes');
        Schema::dropIfExists('player_stats');
        Schema::dropIfExists('player_social_links');
        Schema::dropIfExists('player_profiles');
    }
};
