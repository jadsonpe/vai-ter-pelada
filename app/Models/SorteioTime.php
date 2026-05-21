<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SorteioTime extends Model
{
    protected $fillable = ['sorteio_id', 'nome', 'nome_time', 'ordem'];

    public function sorteio(): BelongsTo
    {
        return $this->belongsTo(Sorteio::class);
    }

    public function jogadores(): HasMany
    {
        return $this->hasMany(SorteioTimeJogador::class);
    }
}
