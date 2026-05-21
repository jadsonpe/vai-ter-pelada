<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Esporte extends Model
{
    protected $fillable = ['nome', 'slug', 'icone', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function peladas(): HasMany
    {
        return $this->hasMany(Pelada::class);
    }

    public function imagemPadraoPath(): string
    {
        return 'images/esportes/'.$this->slug.'.png';
    }

    public function imagemPadraoUrl(): ?string
    {
        $path = $this->imagemPadraoPath();

        return file_exists(public_path($path)) ? asset($path) : null;
    }
}
