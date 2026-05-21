<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaMembro extends Model
{
    protected $fillable = [
        'pelada_id',
        'user_id',
        'apelido',
        'tipo',
        'status',
        'prioridade',
        'data_entrada',
        'observacao',
        'mensalista_desde',
    ];

    protected $casts = [
        'mensalista_desde' => 'date',
        'data_entrada' => 'date',
        'prioridade' => 'integer',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nomeExibicao(): string
    {
        return $this->apelido ?: $this->user?->apelido ?: $this->user?->name ?: 'Jogador';
    }
}
