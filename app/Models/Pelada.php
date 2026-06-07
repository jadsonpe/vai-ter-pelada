<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelada extends Model
{
    public const CATEGORIAS = [
        'adulto' => 'Adulto',
        'infantil' => 'Infantil',
    ];

    protected $fillable = [
        'organizador_id',
        'esporte_id',
        'nome',
        'slug',
        'descricao',
        'data_fundacao',
        'categoria',
        'imagem',
        'cidade',
        'bairro',
        'local_nome',
        'endereco',
        'local',
        'dia_semana',
        'horario',
        'vagas_totais',
        'vagas_diaristas',
        'aceita_diarista',
        'requer_aprovacao',
        'capacidade',
        'valor_mensalista',
        'valor_diarista',
        'status',
        'regras',
        'whatsapp_contato',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'aceita_diarista' => 'boolean',
        'requer_aprovacao' => 'boolean',
        'horario' => 'datetime:H:i',
        'data_fundacao' => 'date',
        'valor_mensalista' => 'decimal:2',
        'valor_diarista' => 'decimal:2',
    ];

    public function categoriaLabel(): string
    {
        return self::CATEGORIAS[$this->categoria ?: 'adulto'] ?? ucfirst((string) $this->categoria);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function esporte(): BelongsTo
    {
        return $this->belongsTo(Esporte::class);
    }

    public function membros(): HasMany
    {
        return $this->hasMany(PeladaMembro::class);
    }

    public function jogos(): HasMany
    {
        return $this->hasMany(PeladaJogo::class);
    }

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(PeladaSolicitacao::class);
    }

    public function caixaMovimentacoes(): HasMany
    {
        return $this->hasMany(PeladaCaixaMovimentacao::class);
    }

    public function torneios(): HasMany
    {
        return $this->hasMany(Torneio::class);
    }

    public function isOwner(?User $user): bool
    {
        return $user && (int) $this->organizador_id === (int) $user->id;
    }

    public function diretorias(): HasMany
    {
        return $this->membros()
            ->where('status', 'ativo')
            ->whereIn('papel', [PeladaMembro::PAPEL_ORGANIZADOR, PeladaMembro::PAPEL_DIRETOR]);
    }

    public function isManagedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin() || $this->isOwner($user)) {
            return true;
        }

        return $this->membros()
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->where('papel', PeladaMembro::PAPEL_DIRETOR)
            ->exists();
    }

    public function papelDoUsuario(?User $user): string
    {
        if ($this->isOwner($user)) {
            return PeladaMembro::PAPEL_ORGANIZADOR;
        }

        if (! $user) {
            return PeladaMembro::PAPEL_JOGADOR;
        }

        return (string) ($this->membros()
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->value('papel') ?: PeladaMembro::PAPEL_JOGADOR);
    }

    public function getVagasTotaisAttribute($value): int
    {
        return (int) ($value ?: $this->capacidade ?: 0);
    }

    public function temImagemPropria(): bool
    {
        return filled($this->imagem);
    }

    public function imagemPropriaUrl(): ?string
    {
        return $this->temImagemPropria() ? asset('storage/'.$this->imagem) : null;
    }

    public function imagemUrl(): ?string
    {
        if ($this->temImagemPropria()) {
            return $this->imagemPropriaUrl();
        }

        return $this->esporte?->imagemPadraoUrl();
    }

    public function mapsUrl(): ?string
    {
        $parts = array_filter([
            $this->local_nome,
            $this->endereco,
            $this->bairro,
            $this->cidade,
        ], fn ($part) => filled($part));

        if ($parts === []) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode(implode(', ', $parts));
    }
}
