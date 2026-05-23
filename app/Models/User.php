<?php

namespace App\Models;

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

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(PeladaSolicitacao::class);
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
}
