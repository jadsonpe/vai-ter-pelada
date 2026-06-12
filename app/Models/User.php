<?php

namespace App\Models;

use App\Models\AvaliacaoPartida;
use App\Notifications\ResetPasswordNotification;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apelido',
        'username',
        'email',
        'pending_email',
        'google_id',
        'password',
        'role',
        'status',
        'plano',
        'limite_peladas',
        'phone',
        'data_nascimento',
        'avatar_url',
        'avatar_path',
        'cidade',
        'bairro',
        'logradouro',
        'numero',
        'complemento',
        'estado',
        'cep',
        'posicao',
        'nivel',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'nivel' => 'integer',
            'limite_peladas' => 'integer',
            'data_nascimento' => 'date',
        ];
    }

    public function peladasOrganizadas(): HasMany
    {
        return $this->hasMany(Pelada::class, 'organizador_id');
    }

    public function peladasGerenciaveis(): Builder
    {
        return Pelada::query()
            ->where(function (Builder $query) {
                $query->where('organizador_id', $this->id)
                    ->orWhereHas('membros', function (Builder $membros) {
                        $membros->where('user_id', $this->id)
                            ->where('status', 'ativo')
                            ->whereIn('papel', [PeladaMembro::PAPEL_ORGANIZADOR, PeladaMembro::PAPEL_DIRETOR]);
                    });
            });
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(PeladaMembro::class);
    }

    public function participacoes(): HasMany
    {
        return $this->hasMany(PeladaJogoParticipante::class);
    }

    public function avaliacoesRecebidas(): HasMany
    {
        return $this->hasMany(AvaliacaoPartida::class, 'avaliado_id');
    }

    public function avaliacoesFeitas(): HasMany
    {
        return $this->hasMany(AvaliacaoPartida::class, 'avaliador_id');
    }

    public function userPoints(): HasMany
    {
        return $this->hasMany(UserPoint::class);
    }

    public function esportePerfis(): HasMany
    {
        return $this->hasMany(UserEsportePerfil::class);
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(PlayerPost::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(PlayerStory::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(PlayerPost::class, 'player_post_likes')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_follows', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_follows', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->whereKey($user->id)->exists();
    }

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(PeladaSolicitacao::class);
    }

    public function getRatingAverageAttribute(): float
    {
        return round($this->avaliacoesRecebidas()->avg('estrelas') ?? 0, 2);
    }

    public function getRatingCountAttribute(): int
    {
        return $this->avaliacoesRecebidas()->count();
    }

    public function getPointsTotalAttribute(): int
    {
        return (int) $this->userPoints()->sum('valor');
    }

    public function hasBadge(string $badgeKey): bool
    {
        return $this->badges()->where('badge_key', $badgeKey)->exists();
    }

    public function awardBadge(string $badgeKey, string $nome, string $descricao): void
    {
        if ($this->hasBadge($badgeKey)) {
            return;
        }

        $this->badges()->create([
            'badge_key' => $badgeKey,
            'nome' => $nome,
            'descricao' => $descricao,
            'conquistado_em' => now(),
        ]);
    }

    public function addPoints(int $valor, string $origem, string $descricao, ?string $referencia = null): void
    {
        $this->userPoints()->create([
            'valor' => $valor,
            'origem' => $origem,
            'descricao' => $descricao,
            'referencia' => $referencia,
        ]);
    }

    public function refreshBadges(): void
    {
        if ($this->ratingCount >= 10 && $this->ratingAverage > 4.5) {
            $this->awardBadge('craque', 'Craque', 'Média superior a 4.5 em pelo menos 10 avaliações recebidas.');
        }

        if ($this->avaliacoesRecebidas()->where('estrelas', '<', 3)->doesntExist() && $this->ratingCount >= 5) {
            $this->awardBadge('fair_play', 'Fair Play', 'Nunca recebeu nota inferior a 3 em avaliações de partidas.');
        }

        if ($this->participacoes()->where('status', 'confirmado')->count() >= 50) {
            $this->awardBadge('lendario', 'Lendário', 'Participou de pelo menos 50 partidas confirmadas.');
        }
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(Presenca::class);
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizador(): bool
    {
        return in_array($this->role, ['admin', 'organizador'], true) || $this->peladasGerenciaveis()->exists();
    }

    public function podeCriarPelada(): bool
    {
        return $this->isAdmin()
            || $this->limite_peladas === 0
            || $this->peladasOrganizadas()->count() < ($this->limite_peladas ?: 1);
    }

    public function camposPerfilPendentes(): array
    {
        $campos = [
            'foto' => $this->avatarUrl(),
            'telefone' => $this->phone,
            'estado' => $this->estado,
            'cidade' => $this->cidade,
            'bairro' => $this->bairro,
        ];

        return array_keys(array_filter($campos, fn ($value) => blank($value)));
    }

    public function perfilCompleto(): bool
    {
        return $this->camposPerfilPendentes() === [];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function avatarUrl(): ?string
    {
        if ($this->avatar_path) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        return $this->avatar_url;
    }

    public function initials(): string
    {
        return Str::of($this->apelido ?: $this->name ?: 'Jogador')
            ->trim()
            ->substr(0, 1)
            ->upper()
            ->toString();
    }

    public function idade(): ?int
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    public function publicProfile(): PlayerProfile
    {
        return PlayerProfile::ensureForUser($this);
    }

    public static function uniqueUsernameFrom(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value, '') ?: 'jogador';
        $base = Str::limit($base, 32, '');
        $candidate = $base;
        $suffix = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('username', $candidate)
            ->exists()) {
            $candidate = Str::limit($base, 32, '').$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
