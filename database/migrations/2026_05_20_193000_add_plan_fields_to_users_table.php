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
            $table->string('plano')->default('gratis')->after('status');
            $table->unsignedSmallInteger('limite_peladas')->default(1)->after('plano');
        });

        DB::table('users')->whereNull('plano')->orWhere('plano', '')->update([
            'plano' => 'gratis',
            'limite_peladas' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plano', 'limite_peladas']);
        });
    }
};
