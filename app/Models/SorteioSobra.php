<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SorteioSobra extends Model
{
    protected $fillable = ['sorteio_id', 'user_id', 'ordem'];

    public function sorteio(): BelongsTo
    {
        return $this->belongsTo(Sorteio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
