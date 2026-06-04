<?php

namespace App\Models;

use App\Models\AvaliacaoPartida;
use Illuminate\Database\Eloquent\Collection;
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

    public function liberadoParaOperacao(): bool
    {
        return ! $this->data_hora || $this->data_hora->copy()->subDay()->isPast();
    }

    public function operacaoLiberaEm(): ?\Illuminate\Support\Carbon
    {
        return $this->data_hora ? $this->data_hora->copy()->subDay() : null;
    }

    public function avaliacoesAbertas(): bool
    {
        return $this->status === 'finalizado'
            && $this->finalizada_em
            && $this->finalizada_em->between(now()->subDays(2), now());
    }

    public function avaliacoesEncerradas(): bool
    {
        return $this->status === 'finalizado'
            && $this->finalizada_em
            && ($this->finalizada_em->copy()->addDays(2)->isPast() || $this->todosElegiveisAvaliaram());
    }

    public function avaliacoesAbertasAte(): ?\Illuminate\Support\Carbon
    {
        return $this->status === 'finalizado' && $this->finalizada_em
            ? $this->finalizada_em->copy()->addDays(2)
            : null;
    }

    public function participantesElegiveisAvaliacao(): Collection
    {
        return $this->participantes()
            ->with(['membro', 'user'])
            ->where('status', 'confirmado')
            ->where('presente_local', true)
            ->whereNotNull('user_id')
            ->whereHas('membro', fn ($query) => $query->whereIn('tipo', ['mensalista', 'diarista']))
            ->get();
    }

    public function todosElegiveisAvaliaram(): bool
    {
        $userIds = $this->participantesElegiveisAvaliacao()
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values();

        if ($userIds->count() < 2) {
            return false;
        }

        $esperadas = $userIds->count() * ($userIds->count() - 1);
        $feitas = $this->avaliacoes()
            ->whereIn('avaliador_id', $userIds)
            ->whereIn('avaliado_id', $userIds)
            ->whereColumn('avaliador_id', '<>', 'avaliado_id')
            ->distinct()
            ->count('id');

        return $feitas >= $esperadas;
    }
}
