<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TorneioTimeJogador extends Model
{
    protected $table = 'torneio_time_jogadores';

    protected $fillable = ['torneio_time_id', 'torneio_participante_id', 'ordem'];

    public function time(): BelongsTo
    {
        return $this->belongsTo(TorneioTime::class, 'torneio_time_id');
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(TorneioParticipante::class, 'torneio_participante_id');
    }
}
