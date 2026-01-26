<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// cria usuarios iniciais do sistema
class UserSeeder extends Seeder
{
    // cria usuarios padrao
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Admin',
                'email' => 'admin@portaldenoticias.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Editor',
                'email' => 'editor@portaldenoticias.com',
                'password' => Hash::make('editor123'),
                'role' => User::ROLE_EDITOR,
            ],
            [
                'name' => 'Jornalista',
                'email' => 'jornalista@portaldenoticias.com',
                'password' => Hash::make('jornalista123'),
                'role' => User::ROLE_JORNALISTA,
            ],
        ]);
    }
}
