<?php

namespace Database\Seeders;

use App\Models\Esporte;
use App\Models\User;
use Database\Seeders\BrazilFullDemoSeeder;
use Database\Seeders\CompleteDemoCoverageSeeder;
use Database\Seeders\DemoDataSeeder;
use Database\Seeders\FiveCompletedPeladasSeeder;
use Database\Seeders\ThirtyMembersPerPeladaSeeder;
use Database\Seeders\ThirtyPeladasSeeder;
use Database\Seeders\TorneiosDemoSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vaiterpelada.test'],
            ['name' => 'Administrador', 'password' => Hash::make('vaiterpelada11'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('vaiterpelada11'),
                'role' => 'admin',
                'status' => 'ativo',
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'organizador@vaiterpelada.test'],
            ['name' => 'Organizador Demo', 'password' => Hash::make('vaiterpelada11'), 'role' => 'organizador']
        );

        foreach (['Futebol', 'Futsal', 'Society'] as $nome) {
            Esporte::firstOrCreate(
                ['slug' => str($nome)->slug()->toString()],
                ['nome' => $nome, 'ativo' => true]
            );
        }

        $this->call(DemoDataSeeder::class);
        $this->call(BrazilFullDemoSeeder::class);
        $this->call(ThirtyPeladasSeeder::class);
        $this->call(ThirtyMembersPerPeladaSeeder::class);
        $this->call(FiveCompletedPeladasSeeder::class);
        $this->call(CompleteDemoCoverageSeeder::class);
        $this->call(TorneiosDemoSeeder::class);
    }
}
