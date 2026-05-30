<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('torneios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->date('data_torneio');
            $table->unsignedTinyInteger('jogadores_por_time')->default(5);
            $table->unsignedTinyInteger('quantidade_times')->default(4);
            $table->string('formato', 40)->default('pontos_corridos');
            $table->string('tipo_confronto', 20)->default('ida');
            $table->boolean('terceiro_lugar')->default(false);
            $table->unsignedTinyInteger('wo_gols_vencedor')->default(3);
            $table->unsignedTinyInteger('wo_gols_perdedor')->default(0);
            $table->boolean('wo_conta_saldo')->default(true);
            $table->string('status', 30)->default('rascunho');
            $table->text('regras')->nullable();
            $table->timestamps();

            $table->index(['pelada_id', 'data_torneio']);
            $table->index(['status', 'data_torneio']);
        });

        Schema::create('torneio_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_id')->constrained('torneios')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pelada_membro_id')->nullable()->constrained('pelada_membros')->nullOnDelete();
            $table->string('nome_manual')->nullable();
            $table->string('tipo', 20)->default('membro');
            $table->boolean('goleiro')->default(false);
            $table->boolean('cabeca_chave')->default(false);
            $table->string('status', 20)->default('ativo');
            $table->timestamps();

            $table->unique(['torneio_id', 'user_id']);
            $table->index(['torneio_id', 'status']);
        });

        Schema::create('torneio_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_id')->constrained('torneios')->cascadeOnDelete();
            $table->string('nome');
            $table->unsignedSmallInteger('ordem')->default(1);
            $table->timestamps();

            $table->unique(['torneio_id', 'nome']);
        });

        Schema::create('torneio_time_jogadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_time_id')->constrained('torneio_times')->cascadeOnDelete();
            $table->foreignId('torneio_participante_id')->constrained('torneio_participantes')->cascadeOnDelete();
            $table->unsignedSmallInteger('ordem')->default(1);
            $table->timestamps();

            $table->unique(['torneio_participante_id']);
        });

        Schema::create('torneio_grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_id')->constrained('torneios')->cascadeOnDelete();
            $table->string('nome');
            $table->unsignedSmallInteger('ordem')->default(1);
            $table->timestamps();
        });

        Schema::create('torneio_grupo_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_grupo_id')->constrained('torneio_grupos')->cascadeOnDelete();
            $table->foreignId('torneio_time_id')->constrained('torneio_times')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['torneio_grupo_id', 'torneio_time_id']);
        });

        Schema::create('torneio_jogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_id')->constrained('torneios')->cascadeOnDelete();
            $table->foreignId('torneio_grupo_id')->nullable()->constrained('torneio_grupos')->nullOnDelete();
            $table->foreignId('time_a_id')->nullable()->constrained('torneio_times')->nullOnDelete();
            $table->foreignId('time_b_id')->nullable()->constrained('torneio_times')->nullOnDelete();
            $table->foreignId('proximo_jogo_id')->nullable()->constrained('torneio_jogos')->nullOnDelete();
            $table->string('fase', 40);
            $table->unsignedSmallInteger('rodada')->default(1);
            $table->unsignedSmallInteger('ordem')->default(1);
            $table->unsignedTinyInteger('gols_a')->nullable();
            $table->unsignedTinyInteger('gols_b')->nullable();
            $table->foreignId('vencedor_id')->nullable()->constrained('torneio_times')->nullOnDelete();
            $table->boolean('decidido_penaltis')->default(false);
            $table->boolean('wo')->default(false);
            $table->foreignId('wo_vencedor_id')->nullable()->constrained('torneio_times')->nullOnDelete();
            $table->foreignId('wo_perdedor_id')->nullable()->constrained('torneio_times')->nullOnDelete();
            $table->string('status', 30)->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['torneio_id', 'fase', 'rodada']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('torneio_gols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_jogo_id')->constrained('torneio_jogos')->cascadeOnDelete();
            $table->foreignId('torneio_time_id')->constrained('torneio_times')->cascadeOnDelete();
            $table->foreignId('torneio_participante_id')->constrained('torneio_participantes')->cascadeOnDelete();
            $table->unsignedTinyInteger('quantidade')->default(1);
            $table->timestamps();
        });

        Schema::create('torneio_cartoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneio_jogo_id')->constrained('torneio_jogos')->cascadeOnDelete();
            $table->foreignId('torneio_time_id')->constrained('torneio_times')->cascadeOnDelete();
            $table->foreignId('torneio_participante_id')->constrained('torneio_participantes')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->unsignedTinyInteger('quantidade')->default(1);
            $table->timestamps();

            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('torneio_cartoes');
        Schema::dropIfExists('torneio_gols');
        Schema::dropIfExists('torneio_jogos');
        Schema::dropIfExists('torneio_grupo_times');
        Schema::dropIfExists('torneio_grupos');
        Schema::dropIfExists('torneio_time_jogadores');
        Schema::dropIfExists('torneio_times');
        Schema::dropIfExists('torneio_participantes');
        Schema::dropIfExists('torneios');
    }
};
