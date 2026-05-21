<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaCaixaMovimentacao extends Model
{
    protected $table = 'pelada_caixa_movimentacoes';

    protected $fillable = [
        'pelada_id',
        'pelada_jogo_id',
        'pelada_membro_id',
        'pelada_jogo_participante_id',
        'user_id',
        'registrado_por',
        'tipo',
        'categoria',
        'descricao',
        'valor',
        'data_pagamento',
        'competencia',
        'forma_pagamento',
        'observacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'competencia' => 'date',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(PeladaMembro::class, 'pelada_membro_id');
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
