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
            'conteudo_ofensivo' => 'Conteúdo ofensivo, discriminatório ou abusivo',
            'golpe_spam' => 'Golpe, spam ou divulgação enganosa',
            'dados_falsos' => 'Informações falsas ou perfil enganoso',
            'risco_seguranca' => 'Risco à segurança dos jogadores',
            'outro' => 'Outro motivo',
        ];

        if ($type === 'pelada') {
            return [
                'pelada_inexistente' => 'Pelada inexistente ou falsa',
                'local_incorreto' => 'Local, horário ou preço incorreto',
                'cobranca_suspeita' => 'Cobrança suspeita ou problema financeiro',
                'organizacao_abusiva' => 'Conduta abusiva da organização',
                ...$common,
            ];
        }

        if ($type === 'publicacao') {
            return [
                'imagem_inadequada' => 'Imagem inadequada ou ofensiva',
                'uso_de_imagem' => 'Uso indevido da imagem de outra pessoa',
                'legenda_abusiva' => 'Legenda abusiva, ofensiva ou discriminatória',
                'spam_publicidade' => 'Spam ou publicidade enganosa',
                ...$common,
            ];
        }

        return [
            'comportamento_abusivo' => 'Comportamento abusivo, ameaça ou assédio',
            'perfil_falso' => 'Perfil falso ou usando dados de outra pessoa',
            'foto_inadequada' => 'Foto ou conteúdo inadequado',
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
