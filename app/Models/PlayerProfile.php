<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'esporte_principal_id',
        'posicao_favorita',
        'nivel_label',
        'reputation_score',
        'headline',
        'bio',
        'banner_path',
        'publico',
    ];

    protected $casts = [
        'publico' => 'boolean',
        'reputation_score' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function esportePrincipal(): BelongsTo
    {
        return $this->belongsTo(Esporte::class, 'esporte_principal_id');
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(PlayerSocialLink::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PlayerStat::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PlayerVote::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(PlayerAchievement::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public static function ensureForUser(User $user): self
    {
        if ($user->playerProfile) {
            return $user->playerProfile;
        }

        return self::create([
            'user_id' => $user->id,
            'slug' => self::uniqueSlug($user->apelido ?: $user->name ?: 'peladeiro'),
            'nivel_label' => 'Perna de Pau',
            'publico' => true,
        ]);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'peladeiro';
        $slug = $base;
        $count = 2;

        while (self::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_path ? Storage::disk('public')->url($this->banner_path) : null;
    }

    public function coverClass(): string
    {
        return match ($this->esportePrincipal?->slug) {
            'basquete' => 'from-orange-500 via-slate-950 to-slate-900',
            'volei' => 'from-sky-500 via-slate-950 to-slate-900',
            'society' => 'from-lime-500 via-slate-950 to-slate-900',
            default => 'from-emerald-500 via-slate-950 to-slate-900',
        };
    }

    public function shareUrl(): string
    {
        return route('peladeiros.show', $this);
    }

    public function shareImageUrl(): string
    {
        return route('peladeiros.card', $this);
    }
}
