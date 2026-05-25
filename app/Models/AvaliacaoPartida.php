<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliacaoPartida extends Model
{
    protected $table = 'avaliacoes_partidas';

    protected $fillable = [
        'pelada_jogo_id',
        'avaliador_id',
        'avaliado_id',
        'estrelas',
        'comentario',
    ];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function avaliador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avaliador_id');
    }

    public function avaliado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avaliado_id');
    }
}
