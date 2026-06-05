<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 40)->nullable()->unique()->after('apelido');
            }
        });

        DB::table('users')
            ->select('id', 'name', 'apelido', 'username')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                $used = DB::table('users')
                    ->whereNotNull('username')
                    ->pluck('username')
                    ->flip();

                foreach ($users as $user) {
                    if ($user->username) {
                        continue;
                    }

                    $base = Str::slug($user->apelido ?: $user->name ?: 'jogador', '');
                    $base = Str::limit($base ?: 'jogador', 32, '');
                    $candidate = $base;
                    $suffix = 1;

                    while ($used->has($candidate)) {
                        $candidate = Str::limit($base, 32, '').$suffix;
                        $suffix++;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['username' => $candidate]);

                    $used->put($candidate, true);
                }
            });

        Schema::table('users', function (Blueprint $table) {
            $table->index('name', 'users_name_idx');
            $table->index('apelido', 'users_apelido_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_idx');
            $table->dropIndex('users_apelido_idx');
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
