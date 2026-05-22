<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sorteio extends Model
{
    protected $fillable = [
        'pelada_jogo_id',
        'criado_por',
        'tipo_sorteio',
        'quantidade_times',
        'jogadores_por_time',
        'usar_ordem_manual',
        'status',
        'realizado_em',
    ];

    protected $casts = [
        'realizado_em' => 'datetime',
        'usar_ordem_manual' => 'boolean',
    ];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function times(): HasMany
    {
        return $this->hasMany(SorteioTime::class)->orderBy('ordem');
    }

    public function sobras(): HasMany
    {
        return $this->hasMany(SorteioSobra::class)->orderBy('ordem');
    }
}
