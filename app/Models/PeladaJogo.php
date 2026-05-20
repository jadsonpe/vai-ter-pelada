<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeladaJogo extends Model
{
    protected $fillable = ['pelada_id', 'titulo', 'data_hora', 'capacidade', 'status'];

    protected $casts = ['data_hora' => 'datetime'];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(PeladaJogoParticipante::class);
    }

    public function sorteios(): HasMany
    {
        return $this->hasMany(Sorteio::class);
    }
}
