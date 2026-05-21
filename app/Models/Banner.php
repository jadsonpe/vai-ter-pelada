<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'titulo',
        'imagem',
        'imagem_url',
        'link',
        'link_url',
        'posicao',
        'ativo',
        'data_inicio',
        'data_fim',
        'inicio_em',
        'fim_em',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'inicio_em' => 'date',
        'fim_em' => 'date',
    ];
}
