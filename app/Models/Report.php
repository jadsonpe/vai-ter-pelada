<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    public const STATUS_PENDING = 'pendente';
    public const STATUS_REVIEWING = 'em_analise';
    public const STATUS_RESOLVED = 'resolvida';
    public const STATUS_REJECTED = 'improcedente';

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resolution',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public static function reasonsFor(string $type): array
    {
        $common = [
            'conteudo_ofensivo' => 'Conteudo ofensivo, discriminatorio ou abusivo',
            'golpe_spam' => 'Golpe, spam ou divulgacao enganosa',
            'dados_falsos' => 'Informacoes falsas ou perfil enganoso',
            'risco_seguranca' => 'Risco a seguranca dos jogadores',
            'outro' => 'Outro motivo',
        ];

        if ($type === 'pelada') {
            return [
                'pelada_inexistente' => 'Pelada inexistente ou falsa',
                'local_incorreto' => 'Local, horario ou preco incorreto',
                'cobranca_suspeita' => 'Cobranca suspeita ou problema financeiro',
                'organizacao_abusiva' => 'Conduta abusiva da organizacao',
                ...$common,
            ];
        }

        if ($type === 'publicacao') {
            return [
                'imagem_inadequada' => 'Imagem inadequada ou ofensiva',
                'uso_de_imagem' => 'Uso indevido da imagem de outra pessoa',
                'legenda_abusiva' => 'Legenda abusiva, ofensiva ou discriminatoria',
                'spam_publicidade' => 'Spam ou publicidade enganosa',
                ...$common,
            ];
        }

        if ($type === 'story') {
            return [
                'midia_inadequada' => 'Foto ou video inadequado ou ofensivo',
                'uso_de_imagem' => 'Uso indevido da imagem de outra pessoa',
                'conteudo_abusivo' => 'Conteudo abusivo, ofensivo ou discriminatorio',
                'spam_publicidade' => 'Spam ou publicidade enganosa',
                ...$common,
            ];
        }

        return [
            'comportamento_abusivo' => 'Comportamento abusivo, ameaca ou assedio',
            'perfil_falso' => 'Perfil falso ou usando dados de outra pessoa',
            'foto_inadequada' => 'Foto ou conteudo inadequado',
            'conduta_antiesportiva' => 'Conduta antiesportiva recorrente',
            ...$common,
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
