<?php

namespace App\Models;

use App\Models\AvaliacaoPartida;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeladaJogo extends Model
{
    protected $fillable = [
        'pelada_id',
        'titulo',
        'data_hora',
        'data_jogo',
        'horario',
        'capacidade',
        'vagas_totais',
        'vagas_diaristas',
        'status',
        'finalizada_em',
        'cancelada_em',
        'observacao',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'data_jogo' => 'date',
        'horario' => 'datetime:H:i',
        'finalizada_em' => 'datetime',
        'cancelada_em' => 'datetime',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(PeladaJogoParticipante::class);
    }

    public function sorteios(): HasMany
    {
        return $this->hasMany(Sorteio::class);
    }

    public function estatisticas(): HasMany
    {
        return $this->hasMany(PeladaJogoParticipanteEstatistica::class);
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(Presenca::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(AvaliacaoPartida::class, 'pelada_jogo_id');
    }

    public function prazoEdicaoEncerrado(): bool
    {
        return $this->data_hora && $this->data_hora->copy()->addDay()->isPast();
    }

    public function bloqueadoParaEdicao(): bool
    {
        return in_array($this->status, ['finalizado', 'cancelado'], true) || $this->prazoEdicaoEncerrado();
    }

    public function avaliacoesAbertas(): bool
    {
        return $this->status === 'finalizado'
            && $this->finalizada_em
            && $this->finalizada_em->between(now()->subDays(2), now());
    }

    public function avaliacoesAbertasAte(): ?\Illuminate\Support\Carbon
    {
        return $this->status === 'finalizado' && $this->finalizada_em
            ? $this->finalizada_em->copy()->addDays(2)
            : null;
    }
}
