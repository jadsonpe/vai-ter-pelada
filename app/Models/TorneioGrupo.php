<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TorneioGrupo extends Model
{
    protected $fillable = ['torneio_id', 'nome', 'ordem'];

    public function torneio(): BelongsTo
    {
        return $this->belongsTo(Torneio::class);
    }

    public function times(): BelongsToMany
    {
        return $this->belongsToMany(TorneioTime::class, 'torneio_grupo_times');
    }

    public function jogos(): HasMany
    {
        return $this->hasMany(TorneioJogo::class);
    }
}
