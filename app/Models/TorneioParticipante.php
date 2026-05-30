<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TorneioParticipante extends Model
{
    protected $fillable = [
        'torneio_id',
        'user_id',
        'pelada_membro_id',
        'nome_manual',
        'tipo',
        'goleiro',
        'cabeca_chave',
        'status',
    ];

    protected $casts = [
        'goleiro' => 'boolean',
        'cabeca_chave' => 'boolean',
    ];

    public function torneio(): BelongsTo
    {
        return $this->belongsTo(Torneio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(PeladaMembro::class, 'pelada_membro_id');
    }

    public function timeJogador(): HasOne
    {
        return $this->hasOne(TorneioTimeJogador::class);
    }

    public function nomeExibicao(): string
    {
        return $this->nome_manual ?: $this->membro?->nomeExibicao() ?: $this->user?->apelido ?: $this->user?->name ?: 'Jogador';
    }
}
