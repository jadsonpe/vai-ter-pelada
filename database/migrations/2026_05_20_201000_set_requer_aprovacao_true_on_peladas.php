<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('peladas')->update(['requer_aprovacao' => true]);
    }

    public function down(): void
    {
        // Não reverte: aprovação passou a ser obrigatória para todas as peladas.
    }
};
