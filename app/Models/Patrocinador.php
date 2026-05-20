<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patrocinador extends Model
{
    protected $table = 'patrocinadores';

    protected $fillable = ['nome', 'logo_url', 'site_url', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];
}
