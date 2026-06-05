<?php

use App\Models\PlayerProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('player_profiles')
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->get(['id'])
                ->each(function ($profile): void {
                    DB::table('player_profiles')
                        ->where('id', $profile->id)
                        ->update(['slug' => '__profile_'.$profile->id]);
                });

            DB::table('player_profiles')
                ->join('users', 'users.id', '=', 'player_profiles.user_id')
                ->whereNotNull('users.username')
                ->where('users.username', '!=', '')
                ->orderBy('player_profiles.id')
                ->get(['player_profiles.id', 'users.username'])
                ->each(function ($profile): void {
                    DB::table('player_profiles')
                        ->where('id', $profile->id)
                        ->update(['slug' => $profile->username]);
                });
        });
    }

    public function down(): void
    {
        DB::table('player_profiles')
            ->join('users', 'users.id', '=', 'player_profiles.user_id')
            ->orderBy('player_profiles.id')
            ->get(['player_profiles.id', 'users.name', 'users.apelido'])
            ->each(function ($profile): void {
                DB::table('player_profiles')
                    ->where('id', $profile->id)
                    ->update([
                        'slug' => PlayerProfile::uniqueSlug($profile->apelido ?: $profile->name ?: 'peladeiro', $profile->id),
                    ]);
            });
    }
};
