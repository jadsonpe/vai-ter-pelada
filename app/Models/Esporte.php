<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Esporte extends Model
{
    protected $fillable = ['nome', 'slug', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function peladas(): HasMany
    {
        return $this->hasMany(Pelada::class);
    }
}
