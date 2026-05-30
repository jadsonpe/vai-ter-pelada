<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TorneioTime extends Model
{
    protected $fillable = ['torneio_id', 'nome', 'ordem'];

    protected $casts = ['ordem' => 'integer'];

    public function torneio(): BelongsTo
    {
        return $this->belongsTo(Torneio::class);
    }

    public function jogadores(): HasMany
    {
        return $this->hasMany(TorneioTimeJogador::class);
    }
}
