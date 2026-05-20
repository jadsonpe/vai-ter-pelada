<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaMembro extends Model
{
    protected $fillable = ['pelada_id', 'user_id', 'tipo', 'status', 'mensalista_desde'];

    protected $casts = ['mensalista_desde' => 'date'];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
