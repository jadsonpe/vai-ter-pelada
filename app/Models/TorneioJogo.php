<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TorneioJogo extends Model
{
    protected $fillable = [
        'torneio_id',
        'torneio_grupo_id',
        'time_a_id',
        'time_b_id',
        'proximo_jogo_id',
        'fase',
        'rodada',
        'ordem',
        'gols_a',
        'gols_b',
        'vencedor_id',
        'decidido_penaltis',
        'wo',
        'wo_vencedor_id',
        'wo_perdedor_id',
        'status',
        'observacao',
    ];

    protected $casts = [
        'decidido_penaltis' => 'boolean',
        'wo' => 'boolean',
        'gols_a' => 'integer',
        'gols_b' => 'integer',
    ];

    public function torneio(): BelongsTo
    {
        return $this->belongsTo(Torneio::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(TorneioGrupo::class, 'torneio_grupo_id');
    }

    public function timeA(): BelongsTo
    {
        return $this->belongsTo(TorneioTime::class, 'time_a_id');
    }

    public function timeB(): BelongsTo
    {
        return $this->belongsTo(TorneioTime::class, 'time_b_id');
    }

    public function vencedor(): BelongsTo
    {
        return $this->belongsTo(TorneioTime::class, 'vencedor_id');
    }

    public function gols(): HasMany
    {
        return $this->hasMany(TorneioGol::class);
    }

    public function cartoes(): HasMany
    {
        return $this->hasMany(TorneioCartao::class);
    }

    public function isEliminatorio(): bool
    {
        return ! in_array($this->fase, ['pontos_corridos', 'grupo'], true);
    }
}
