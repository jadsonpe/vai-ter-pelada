<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Esporte extends Model
{
    public const ALLOWED_SLUGS = ['futebol', 'futsal', 'society'];

    public const FOOTBALL_MODALITIES = [
        'futebol' => 'Futebol',
        'futsal' => 'Futsal',
        'society' => 'Society',
    ];

    protected $fillable = ['nome', 'slug', 'icone', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function peladas(): HasMany
    {
        return $this->hasMany(Pelada::class);
    }

    public function scopePermitidos($query)
    {
        return $query->whereIn('slug', self::ALLOWED_SLUGS);
    }

    public static function ensureFootballModalities()
    {
        foreach (self::FOOTBALL_MODALITIES as $slug => $nome) {
            self::updateOrCreate(
                ['slug' => $slug],
                ['nome' => $nome, 'ativo' => true]
            );
        }

        return self::permitidos()->where('ativo', true)->orderBy('nome')->get();
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
