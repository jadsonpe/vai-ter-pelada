<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PlayerStoryItem extends Model
{
    protected $fillable = [
        'player_story_id',
        'type',
        'media_path',
        'thumbnail_path',
        'mime_type',
        'size_bytes',
        'duration_seconds',
        'sort_order',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'duration_seconds' => 'integer',
        'sort_order' => 'integer',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(PlayerStory::class, 'player_story_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(PlayerStoryView::class);
    }

    public function mediaUrl(): string
    {
        return Storage::disk('public')->url($this->media_path);
    }

    public function thumbnailUrl(): string
    {
        return Storage::disk('public')->url($this->thumbnail_path ?: $this->media_path);
    }
}
