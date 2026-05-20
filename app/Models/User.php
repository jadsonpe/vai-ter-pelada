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
        'email',
        'password',
        'role',
        'phone',
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizador(): bool
    {
        return in_array($this->role, ['admin', 'organizador'], true);
    }
}
