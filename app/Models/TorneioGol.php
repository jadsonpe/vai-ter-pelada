<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TorneioGol extends Model
{
    protected $fillable = ['torneio_jogo_id', 'torneio_time_id', 'torneio_participante_id', 'quantidade'];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(TorneioJogo::class, 'torneio_jogo_id');
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(TorneioTime::class, 'torneio_time_id');
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(TorneioParticipante::class, 'torneio_participante_id');
    }
}
