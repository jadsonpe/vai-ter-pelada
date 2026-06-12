<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_profile_id')->constrained()->cascadeOnDelete();
            $table->string('caption', 220)->nullable();
            $table->string('visibility', 30)->default('public');
            $table->string('status', 30)->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at']);
            $table->index(['player_profile_id', 'status', 'expires_at'], 'player_stories_profile_status_expires_idx');
        });

        Schema::create('player_story_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_story_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('image');
            $table->string('media_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('mime_type', 80)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['player_story_id', 'sort_order']);
        });

        Schema::create('player_story_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_story_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->unique(['player_story_item_id', 'viewer_id'], 'player_story_views_item_viewer_unique');
            $table->index(['viewer_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_story_views');
        Schema::dropIfExists('player_story_items');
        Schema::dropIfExists('player_stories');
    }
};
