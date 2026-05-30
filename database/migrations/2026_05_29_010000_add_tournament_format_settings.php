<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneios', function (Blueprint $table) {
            $table->unsignedTinyInteger('quantidade_grupos')->nullable()->after('tipo_confronto');
            $table->unsignedTinyInteger('classificados_total')->nullable()->after('quantidade_grupos');
            $table->unsignedTinyInteger('classificados_por_grupo')->nullable()->after('classificados_total');
            $table->string('tipo_confronto_mata_mata', 20)->default('unico')->after('classificados_por_grupo');
            $table->string('tipo_confronto_final', 20)->default('unico')->after('tipo_confronto_mata_mata');
        });
    }

    public function down(): void
    {
        Schema::table('torneios', function (Blueprint $table) {
            $table->dropColumn([
                'quantidade_grupos',
                'classificados_total',
                'classificados_por_grupo',
                'tipo_confronto_mata_mata',
                'tipo_confronto_final',
            ]);
        });
    }
};
