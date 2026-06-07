<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeladaMembro extends Model
{
    public const PAPEL_ORGANIZADOR = 'organizador';
    public const PAPEL_DIRETOR = 'diretor';
    public const PAPEL_JOGADOR = 'jogador';

    protected $fillable = [
        'pelada_id',
        'user_id',
        'apelido',
        'tipo',
        'papel',
        'status',
        'prioridade',
        'data_entrada',
        'observacao',
        'mensalista_desde',
    ];

    protected $casts = [
        'mensalista_desde' => 'date',
        'data_entrada' => 'date',
        'prioridade' => 'integer',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nomeExibicao(): string
    {
        return $this->user?->apelido ?: $this->user?->name ?: 'Jogador';
    }

    public function isOrganizador(): bool
    {
        return $this->papel === self::PAPEL_ORGANIZADOR;
    }

    public function isDiretor(): bool
    {
        return $this->papel === self::PAPEL_DIRETOR;
    }

    public function podeGerenciarPelada(): bool
    {
        return $this->status === 'ativo'
            && in_array($this->papel, [self::PAPEL_ORGANIZADOR, self::PAPEL_DIRETOR], true);
    }

    public function papelLabel(): string
    {
        return match ($this->papel) {
            self::PAPEL_ORGANIZADOR => 'Organizador',
            self::PAPEL_DIRETOR => 'Diretor',
            default => 'Jogador',
        };
    }
}
