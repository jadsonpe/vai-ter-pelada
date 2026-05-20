<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaSolicitacao extends Model
{
    protected $table = 'pelada_solicitacoes';

    protected $fillable = ['pelada_id', 'user_id', 'tipo', 'status', 'mensagem', 'avaliado_por', 'avaliado_em'];

    protected $casts = ['avaliado_em' => 'datetime'];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
