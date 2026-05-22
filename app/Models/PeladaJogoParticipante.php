<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaJogoParticipante extends Model
{
    protected $fillable = [
        'pelada_jogo_id',
        'user_id',
        'pelada_membro_id',
        'tipo',
        'tipo_no_jogo',
        'status',
        'ordem_chegada',
        'posicao_fila',
        'confirmado_em',
        'cancelado_em',
        'presente_local',
        'ordem_presenca',
        'nome_avulso',
    ];

    protected $casts = [
        'confirmado_em' => 'datetime',
        'cancelado_em' => 'datetime',
        'presente_local' => 'boolean',
    ];

    public function nomeExibicao(): string
    {
        if (filled($this->nome_avulso)) {
            return $this->nome_avulso;
        }

        return $this->membro?->nomeExibicao()
            ?: $this->user?->name
            ?: 'Jogador';
    }

    public function isAvulso(): bool
    {
        return filled($this->nome_avulso) && ! $this->user_id;
    }

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(PeladaMembro::class, 'pelada_membro_id');
    }
}
