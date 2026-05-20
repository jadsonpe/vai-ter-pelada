<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['titulo', 'imagem_url', 'link_url', 'posicao', 'ativo', 'inicio_em', 'fim_em'];

    protected $casts = [
        'ativo' => 'boolean',
        'inicio_em' => 'date',
        'fim_em' => 'date',
    ];
}
