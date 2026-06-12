<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PlayerStory extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REMOVED = 'removed';

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_FOLLOWERS = 'followers';

    public const MAX_ACTIVE_STORIES = 10;

    protected $fillable = [
        'user_id',
        'player_profile_id',
        'caption',
        'visibility',
        'status',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where('expires_at', '>', now());
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->where('visibility', self::VISIBILITY_PUBLIC)
                ->orWhere('user_id', $user->id)
                ->orWhere(function (Builder $query) use ($user) {
                    $query->where('visibility', self::VISIBILITY_FOLLOWERS)
                        ->whereHas('user.followers', fn (Builder $followers) => $followers->whereKey($user->id));
                });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PlayerProfile::class, 'player_profile_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PlayerStoryItem::class)->orderBy('sort_order');
    }

    public function canBeManagedBy(?User $user): bool
    {
        return $user && ((int) $this->user_id === (int) $user->id || $user->isAdmin());
    }

    public function removeMedia(): void
    {
        $paths = $this->items
            ->flatMap(fn (PlayerStoryItem $item) => [$item->media_path, $item->thumbnail_path])
            ->filter()
            ->all();

        Storage::disk('public')->delete($paths);
    }
}
