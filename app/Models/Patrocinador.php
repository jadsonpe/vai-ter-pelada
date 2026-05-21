<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patrocinador extends Model
{
    protected $table = 'patrocinadores';

    protected $fillable = ['nome', 'logo', 'logo_url', 'link', 'site_url', 'telefone', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];
}
