<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelada_membros', function (Blueprint $table) {
            $table->string('papel')->default('jogador')->after('tipo')->index();
        });

        DB::table('pelada_membros')
            ->join('peladas', 'peladas.id', '=', 'pelada_membros.pelada_id')
            ->whereColumn('pelada_membros.user_id', 'peladas.organizador_id')
            ->update(['pelada_membros.papel' => 'organizador']);

        $organizadorIds = DB::table('peladas')
            ->whereNotNull('organizador_id')
            ->pluck('organizador_id')
            ->unique()
            ->all();

        if ($organizadorIds !== []) {
            DB::table('users')
                ->whereIn('id', $organizadorIds)
                ->where('role', 'jogador')
                ->update(['role' => 'organizador']);
        }
    }

    public function down(): void
    {
        Schema::table('pelada_membros', function (Blueprint $table) {
            $table->dropIndex(['papel']);
            $table->dropColumn('papel');
        });
    }
};
