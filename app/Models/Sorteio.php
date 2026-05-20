<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sorteio extends Model
{
    protected $fillable = ['pelada_jogo_id', 'criado_por', 'quantidade_times', 'realizado_em'];

    protected $casts = ['realizado_em' => 'datetime'];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(PeladaJogo::class, 'pelada_jogo_id');
    }

    public function times(): HasMany
    {
        return $this->hasMany(SorteioTime::class);
    }
}
