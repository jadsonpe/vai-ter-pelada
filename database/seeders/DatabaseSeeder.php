<?php

namespace Database\Seeders;

use App\Models\Esporte;
use App\Models\User;
use Database\Seeders\BrazilFullDemoSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vaiterpelada.test'],
            ['name' => 'Administrador', 'password' => Hash::make('asfdvaiterpelada11'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('asfdvaiterpelada11'),
                'role' => 'admin',
                'status' => 'ativo',
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'organizador@vaiterpelada.test'],
            ['name' => 'Organizador Demo', 'password' => Hash::make('asfdvaiterpelada11'), 'role' => 'organizador']
        );

        foreach (['Futebol', 'Futsal', 'Society', 'Volei', 'Basquete'] as $nome) {
            Esporte::firstOrCreate(
                ['slug' => str($nome)->slug()->toString()],
                ['nome' => $nome, 'ativo' => true]
            );
        }

        $this->call(DemoDataSeeder::class);
        $this->call(BrazilFullDemoSeeder::class);
    }
}
