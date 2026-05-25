<?php

namespace Database\Seeders;

use App\Models\Esporte;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vaiterpelada.test'],
            ['name' => 'Administrador', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'organizador@vaiterpelada.test'],
            ['name' => 'Organizador Demo', 'password' => Hash::make('password'), 'role' => 'organizador']
        );

        foreach (['Futebol', 'Futsal', 'Society', 'Volei', 'Basquete'] as $nome) {
            Esporte::firstOrCreate(
                ['slug' => str($nome)->slug()->toString()],
                ['nome' => $nome, 'ativo' => true]
            );
        }

        $this->call(DemoDataSeeder::class);
    }
}
