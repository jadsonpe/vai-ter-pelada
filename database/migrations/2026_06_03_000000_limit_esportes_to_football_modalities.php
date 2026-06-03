<?php

use App\Models\Esporte;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('esportes')
            ->whereNotIn('slug', Esporte::ALLOWED_SLUGS)
            ->update(['ativo' => false]);
    }

    public function down(): void
    {
        //
    }
};
