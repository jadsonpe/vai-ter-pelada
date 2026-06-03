<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaJogoParticipanteEstatistica extends Model
{
    protected $fillable = [
        'pelada_jogo_id',
        'pelada_jogo_participante_id',
        'user_id',
        'gols',
        'cartoes_amarelos',
        'cartoes_vermelhos',
        'cartoes_azuis',
        'observacao',
    ];

    protected $casts = [
        'gols' => 'integer',
        'cartoes_amarelos' => 'integer',
        'cartoes_vermelhos' => 'integer',
        'cartoes_azuis' => 'integer',
    ];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(PeladaJogoParticipante::class, 'pelada_jogo_participante_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
