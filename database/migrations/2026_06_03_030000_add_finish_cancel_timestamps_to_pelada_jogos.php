<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelada_jogos', function (Blueprint $table) {
            if (! Schema::hasColumn('pelada_jogos', 'finalizada_em')) {
                $table->timestamp('finalizada_em')->nullable()->after('status');
            }

            if (! Schema::hasColumn('pelada_jogos', 'cancelada_em')) {
                $table->timestamp('cancelada_em')->nullable()->after('finalizada_em');
            }
        });

        DB::table('pelada_jogos')
            ->where('status', 'finalizado')
            ->whereNull('finalizada_em')
            ->update(['finalizada_em' => DB::raw('COALESCE(updated_at, data_hora, NOW())')]);

        DB::table('pelada_jogos')
            ->where('status', 'cancelado')
            ->whereNull('cancelada_em')
            ->update(['cancelada_em' => DB::raw('COALESCE(updated_at, data_hora, NOW())')]);
    }

    public function down(): void
    {
        Schema::table('pelada_jogos', function (Blueprint $table) {
            if (Schema::hasColumn('pelada_jogos', 'cancelada_em')) {
                $table->dropColumn('cancelada_em');
            }

            if (Schema::hasColumn('pelada_jogos', 'finalizada_em')) {
                $table->dropColumn('finalizada_em');
            }
        });
    }
};
