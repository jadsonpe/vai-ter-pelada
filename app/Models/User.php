<?php

namespace App\Models;

use App\Models\AvaliacaoPartida;
use App\Notifications\ResetPasswordNotification;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apelido',
        'email',
        'google_id',
        'password',
        'role',
        'status',
        'plano',
        'limite_peladas',
        'phone',
        'avatar_url',
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
        ];
    }

    public function peladasOrganizadas(): HasMany
    {
        return $this->hasMany(Pelada::class, 'organizador_id');
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

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
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
        return in_array($this->role, ['admin', 'organizador'], true) || $this->peladasOrganizadas()->exists();
    }

    public function podeCriarPelada(): bool
    {
        return $this->isAdmin()
            || $this->limite_peladas === 0
            || $this->peladasOrganizadas()->count() < ($this->limite_peladas ?: 1);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
