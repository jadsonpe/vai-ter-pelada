<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('torneios', function (Blueprint $table) {
            $table->string('imagem')->nullable()->after('regras');
            $table->json('mural_fotos')->nullable()->after('imagem');
        });
    }

    public function down(): void
    {
        Schema::table('torneios', function (Blueprint $table) {
            $table->dropColumn(['imagem', 'mural_fotos']);
        });
    }
};
