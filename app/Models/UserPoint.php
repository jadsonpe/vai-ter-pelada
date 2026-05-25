<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'origem',
        'valor',
        'descricao',
        'referencia',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
