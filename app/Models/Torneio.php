<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneio extends Model
{
    public const FORMATOS = [
        'pontos_corridos' => 'Grupo unico + fase final',
        'grupos_mata_mata' => 'Multi-grupos + fase final',
        'mata_mata' => 'Mata-mata direto',
    ];

    protected $fillable = [
        'pelada_id',
        'nome',
        'slug',
        'data_torneio',
        'jogadores_por_time',
        'quantidade_times',
        'formato',
        'tipo_confronto',
        'quantidade_grupos',
        'classificados_total',
        'classificados_por_grupo',
        'tipo_confronto_mata_mata',
        'tipo_confronto_final',
        'terceiro_lugar',
        'wo_gols_vencedor',
        'wo_gols_perdedor',
        'wo_conta_saldo',
        'status',
        'regras',
    ];

    protected $casts = [
        'data_torneio' => 'date',
        'terceiro_lugar' => 'boolean',
        'wo_conta_saldo' => 'boolean',
        'jogadores_por_time' => 'integer',
        'quantidade_times' => 'integer',
        'quantidade_grupos' => 'integer',
        'classificados_total' => 'integer',
        'classificados_por_grupo' => 'integer',
        'wo_gols_vencedor' => 'integer',
        'wo_gols_perdedor' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(TorneioParticipante::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(TorneioTime::class);
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(TorneioGrupo::class);
    }

    public function jogos(): HasMany
    {
        return $this->hasMany(TorneioJogo::class);
    }

    public function formatoLabel(): string
    {
        return self::FORMATOS[$this->formato] ?? ucfirst(str_replace('_', ' ', $this->formato));
    }
}
