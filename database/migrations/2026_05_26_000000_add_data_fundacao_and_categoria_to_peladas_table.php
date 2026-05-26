<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peladas', function (Blueprint $table) {
            if (! Schema::hasColumn('peladas', 'data_fundacao')) {
                $table->date('data_fundacao')->nullable()->after('descricao');
            }

            if (! Schema::hasColumn('peladas', 'categoria')) {
                $table->string('categoria', 20)->default('adulto')->after('data_fundacao');
                $table->index('categoria');
            }
        });

        DB::table('peladas')
            ->whereNull('categoria')
            ->orWhere('categoria', '')
            ->update(['categoria' => 'adulto']);
    }

    public function down(): void
    {
        Schema::table('peladas', function (Blueprint $table) {
            if (Schema::hasColumn('peladas', 'categoria')) {
                $table->dropIndex(['categoria']);
                $table->dropColumn('categoria');
            }

            if (Schema::hasColumn('peladas', 'data_fundacao')) {
                $table->dropColumn('data_fundacao');
            }
        });
    }
};
