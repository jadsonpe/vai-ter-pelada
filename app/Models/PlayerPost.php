<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class PlayerPost extends Model
{
    public const STATUS_PUBLICADO = 'publicado';
    public const STATUS_REMOVIDO = 'removido';

    public const MAX_ACTIVE_POSTS = 5;

    protected $fillable = [
        'user_id',
        'player_profile_id',
        'tipo',
        'categoria',
        'legenda',
        'media_path',
        'thumbnail_path',
        'mime_type',
        'tamanho_bytes',
        'duracao_segundos',
        'status',
        'publicado_em',
    ];

    protected $casts = [
        'publicado_em' => 'datetime',
        'tamanho_bytes' => 'integer',
        'duracao_segundos' => 'integer',
    ];

    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLICADO);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PlayerProfile::class, 'player_profile_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_post_likes')->withTimestamps();
    }

    public function mediaUrl(): string
    {
        return Storage::disk('public')->url($this->media_path);
    }

    public function thumbnailUrl(): string
    {
        return Storage::disk('public')->url($this->thumbnail_path ?: $this->media_path);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user && (int) $this->user_id === (int) $user->id;
    }

    public function canBeManagedBy(?User $user): bool
    {
        return $this->isOwnedBy($user) || (bool) $user?->isAdmin();
    }

    public function removeMedia(): void
    {
        Storage::disk('public')->delete(array_filter([$this->media_path, $this->thumbnail_path]));
    }
}
