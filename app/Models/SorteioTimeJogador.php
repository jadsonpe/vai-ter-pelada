<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SorteioTimeJogador extends Model
{
    protected $table = 'sorteio_time_jogadores';

    protected $fillable = ['sorteio_time_id', 'user_id', 'ordem'];

    public function time(): BelongsTo
    {
        return $this->belongsTo(SorteioTime::class, 'sorteio_time_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
