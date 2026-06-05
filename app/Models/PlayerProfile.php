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
        'banner_preset',
        'banner_theme',
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
        if ($user->relationLoaded('playerProfile') && $user->playerProfile) {
            if ($user->username && $user->playerProfile->slug !== $user->username) {
                $user->playerProfile->forceFill(['slug' => $user->username])->save();
            }

            return $user->playerProfile;
        }

        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'slug' => $user->username ?: self::uniqueSlug($user->apelido ?: $user->name ?: 'peladeiro'),
                'nivel_label' => 'Novato',
                'publico' => true,
            ]
        );
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

    public static function levelForScore(int $score): string
    {
        return match (true) {
            $score >= 700 => 'Dono da Bola',
            $score >= 500 => 'Rei da Quadra',
            $score >= 300 => 'Craque do Baba',
            $score >= 120 => 'Reserva de Luxo',
            default => 'Novato',
        };
    }

    public function bannerUrl(): ?string
    {
        if ($this->banner_preset) {
            return asset('images/player-covers/'.$this->banner_preset);
        }

        return null;
    }

    public function coverUrl(): string
    {
        return $this->bannerUrl() ?: '';
    }

    public static function imageCoverOptions(): array
    {
        return [
            'f1.png' => 'Futebol 1',
            'f2.png' => 'Futebol 2',
            'f3.png' => 'Futebol 3',
            'f4.png' => 'Futebol 4',
            'futsal1.png' => 'Futsal 1',
            'futsal2.png' => 'Futsal 2',
            'futsal3.png' => 'Futsal 3',
            's1.png' => 'Society 1',
            's2.png' => 'Society 2',
            'society3.png' => 'Society 3',
        ];
    }

    public static function coverOptions(): array
    {
        return self::imageCoverOptions();
    }

    public static function gradientCoverOptions(): array
    {
        return [
            'verde_campo' => [
                'label' => 'Verde campo',
                'style' => 'linear-gradient(135deg, #10b981 0%, #064e3b 42%, #020617 100%)',
            ],
            'noturno' => [
                'label' => 'Noturno',
                'style' => 'linear-gradient(135deg, #0f172a 0%, #1e293b 48%, #020617 100%)',
            ],
            'fogo' => [
                'label' => 'Energia',
                'style' => 'linear-gradient(135deg, #f97316 0%, #be123c 46%, #020617 100%)',
            ],
            'quadra' => [
                'label' => 'Quadra azul',
                'style' => 'linear-gradient(135deg, #38bdf8 0%, #1d4ed8 45%, #020617 100%)',
            ],
            'ouro' => [
                'label' => 'Ouro',
                'style' => 'linear-gradient(135deg, #facc15 0%, #15803d 45%, #020617 100%)',
            ],
            'roxo' => [
                'label' => 'Competitivo',
                'style' => 'linear-gradient(135deg, #a855f7 0%, #0f766e 48%, #020617 100%)',
            ],
        ];
    }

    public function defaultCoverTheme(): string
    {
        $sport = Str::lower($this->esportePrincipal?->slug ?: $this->esportePrincipal?->nome ?: '');

        return match (true) {
            str_contains($sport, 'futsal') => 'noturno',
            str_contains($sport, 'society') => 'ouro',
            default => 'verde_campo',
        };
    }

    public function coverStyle(): string
    {
        if ($this->bannerUrl()) {
            return "background-image: linear-gradient(90deg, rgba(2,6,23,.92), rgba(2,6,23,.45)), url('{$this->bannerUrl()}'); background-size: cover; background-position: center;";
        }

        $theme = self::gradientCoverOptions()[$this->banner_theme ?: $this->defaultCoverTheme()]
            ?? self::gradientCoverOptions()['verde_campo'];

        return "background-image: {$theme['style']};";
    }

    public function coverClass(): string
    {
        return match ($this->esportePrincipal?->slug) {
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
        return route('peladeiros.card', [
            'profile' => $this,
            'v' => optional($this->updated_at)->timestamp ?: time(),
        ]);
    }
}
