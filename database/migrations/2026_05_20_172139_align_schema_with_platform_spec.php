<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apelido')->nullable()->after('name');
            $table->string('cidade')->nullable()->after('phone');
            $table->string('bairro')->nullable()->after('cidade');
            $table->string('posicao')->nullable()->after('bairro');
            $table->unsignedTinyInteger('nivel')->nullable()->after('posicao');
            $table->string('status')->default('ativo')->after('role');
        });

        Schema::table('esportes', function (Blueprint $table) {
            $table->string('icone')->nullable()->after('slug');
        });

        Schema::table('peladas', function (Blueprint $table) {
            $table->string('cidade')->nullable()->after('descricao');
            $table->string('bairro')->nullable()->after('cidade');
            $table->string('local_nome')->nullable()->after('bairro');
            $table->string('endereco')->nullable()->after('local_nome');
            $table->unsignedSmallInteger('vagas_totais')->nullable()->after('horario');
            $table->unsignedSmallInteger('vagas_diaristas')->default(0)->after('vagas_totais');
            $table->boolean('aceita_diarista')->default(true)->after('vagas_diaristas');
            $table->boolean('requer_aprovacao')->default(false)->after('aceita_diarista');
            $table->string('status')->default('ativa')->after('requer_aprovacao');
            $table->text('regras')->nullable()->after('status');
            $table->string('whatsapp_contato')->nullable()->after('regras');
        });

        DB::statement("UPDATE peladas SET vagas_totais = capacidade WHERE vagas_totais IS NULL");
        DB::statement("UPDATE peladas SET local_nome = local WHERE local_nome IS NULL");

        Schema::table('pelada_membros', function (Blueprint $table) {
            $table->unsignedInteger('prioridade')->default(0)->after('status');
            $table->date('data_entrada')->nullable()->after('prioridade');
            $table->text('observacao')->nullable()->after('data_entrada');
        });

        DB::statement("ALTER TABLE pelada_membros MODIFY status ENUM('ativo','pendente','bloqueado','saiu','inativo') NOT NULL DEFAULT 'ativo'");

        Schema::table('pelada_jogos', function (Blueprint $table) {
            $table->date('data_jogo')->nullable()->after('titulo');
            $table->time('horario')->nullable()->after('data_jogo');
            $table->unsignedSmallInteger('vagas_totais')->nullable()->after('horario');
            $table->unsignedSmallInteger('vagas_diaristas')->default(0)->after('vagas_totais');
            $table->text('observacao')->nullable()->after('status');
        });

        DB::statement("UPDATE pelada_jogos SET data_jogo = DATE(data_hora), horario = TIME(data_hora), vagas_totais = capacidade WHERE data_jogo IS NULL");
        DB::statement("ALTER TABLE pelada_jogos MODIFY status ENUM('aberto','fechado','finalizado','cancelado','realizado') NOT NULL DEFAULT 'aberto'");

        Schema::table('pelada_jogo_participantes', function (Blueprint $table) {
            $table->string('tipo_no_jogo')->default('diarista')->after('pelada_membro_id');
            $table->unsignedInteger('ordem_chegada')->nullable()->after('status');
        });

        DB::statement("UPDATE pelada_jogo_participantes SET tipo_no_jogo = tipo, ordem_chegada = posicao_fila WHERE ordem_chegada IS NULL");
        DB::statement("ALTER TABLE pelada_jogo_participantes MODIFY tipo ENUM('mensalista','diarista','convidado') NOT NULL DEFAULT 'diarista'");
        DB::statement("ALTER TABLE pelada_jogo_participantes MODIFY status ENUM('confirmado','fila','cancelado','faltou','compareceu') NOT NULL DEFAULT 'confirmado'");

        Schema::table('pelada_solicitacoes', function (Blueprint $table) {
            $table->string('tipo_solicitacao')->default('virar_mensalista')->after('user_id');
            $table->foreignId('respondido_por')->nullable()->after('mensagem')->constrained('users')->nullOnDelete();
            $table->timestamp('respondido_em')->nullable()->after('respondido_por');
        });

        DB::statement("UPDATE pelada_solicitacoes SET tipo_solicitacao = IF(tipo = 'mensalista', 'virar_mensalista', tipo)");

        Schema::table('sorteios', function (Blueprint $table) {
            $table->string('tipo_sorteio')->default('simples')->after('criado_por');
            $table->string('status')->default('publicado')->after('quantidade_times');
        });

        Schema::table('sorteio_times', function (Blueprint $table) {
            $table->string('nome_time')->nullable()->after('sorteio_id');
        });

        DB::statement("UPDATE sorteio_times SET nome_time = nome WHERE nome_time IS NULL");

        Schema::table('banners', function (Blueprint $table) {
            $table->string('imagem')->nullable()->after('titulo');
            $table->string('link')->nullable()->after('imagem');
            $table->date('data_inicio')->nullable()->after('posicao');
            $table->date('data_fim')->nullable()->after('data_inicio');
        });

        DB::statement("UPDATE banners SET imagem = imagem_url, link = link_url, data_inicio = inicio_em, data_fim = fim_em");

        Schema::table('patrocinadores', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('nome');
            $table->string('link')->nullable()->after('logo');
            $table->string('telefone')->nullable()->after('link');
        });

        DB::statement("UPDATE patrocinadores SET logo = logo_url, link = site_url");

        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_jogo_id')->constrained('pelada_jogos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['compareceu', 'faltou', 'justificou'])->default('compareceu');
            $table->foreignId('marcado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['pelada_jogo_id', 'user_id']);
        });

        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_jogo_id')->constrained('pelada_jogos')->cascadeOnDelete();
            $table->foreignId('avaliador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('avaliado_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('nota');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
        Schema::dropIfExists('presencas');

        Schema::table('patrocinadores', fn (Blueprint $table) => $table->dropColumn(['logo', 'link', 'telefone']));
        Schema::table('banners', fn (Blueprint $table) => $table->dropColumn(['imagem', 'link', 'data_inicio', 'data_fim']));
        Schema::table('sorteio_times', fn (Blueprint $table) => $table->dropColumn('nome_time'));
        Schema::table('sorteios', fn (Blueprint $table) => $table->dropColumn(['tipo_sorteio', 'status']));
        Schema::table('pelada_solicitacoes', fn (Blueprint $table) => $table->dropConstrainedForeignId('respondido_por'));
        Schema::table('pelada_solicitacoes', fn (Blueprint $table) => $table->dropColumn(['tipo_solicitacao', 'respondido_em']));
        Schema::table('pelada_jogo_participantes', fn (Blueprint $table) => $table->dropColumn(['tipo_no_jogo', 'ordem_chegada']));
        Schema::table('pelada_jogos', fn (Blueprint $table) => $table->dropColumn(['data_jogo', 'horario', 'vagas_totais', 'vagas_diaristas', 'observacao']));
        Schema::table('pelada_membros', fn (Blueprint $table) => $table->dropColumn(['prioridade', 'data_entrada', 'observacao']));
        Schema::table('peladas', fn (Blueprint $table) => $table->dropColumn(['cidade', 'bairro', 'local_nome', 'endereco', 'vagas_totais', 'vagas_diaristas', 'aceita_diarista', 'requer_aprovacao', 'status', 'regras', 'whatsapp_contato']));
        Schema::table('esportes', fn (Blueprint $table) => $table->dropColumn('icone'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['apelido', 'cidade', 'bairro', 'posicao', 'nivel', 'status']));
    }
};
