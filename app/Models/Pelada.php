<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelada extends Model
{
    protected $fillable = [
        'organizador_id',
        'esporte_id',
        'nome',
        'slug',
        'descricao',
        'local',
        'dia_semana',
        'horario',
        'capacidade',
        'valor_mensalista',
        'valor_diarista',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'horario' => 'datetime:H:i',
        'valor_mensalista' => 'decimal:2',
        'valor_diarista' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }

    public function membros(): HasMany
    {
        return $this->hasMany(PeladaMembro::class);
    }

    public function jogos(): HasMany
    {
        return $this->hasMany(PeladaJogo::class);
    }

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(PeladaSolicitacao::class);
    }
}
