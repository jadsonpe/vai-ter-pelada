<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerVote extends Model
{
    protected $fillable = ['player_profile_id', 'voter_id', 'pelada_jogo_id', 'type', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PlayerProfile::class, 'player_profile_id');
    }
}
