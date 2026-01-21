<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        User::insert([
            [
                'name' => 'Admin',
                'email' => 'admin@portaldenoticias.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'name' => 'editor',
                'email' => 'editor@portaldenoticias.com',
                'password' => Hash::make('editor123'),
                'role' => 'editor',
            ],
            [
                'name' => 'jornalista',
                'email' => 'jornalista@portaldenoticias.com',
                'password' => Hash::make('jornalista123'),
                'role' => 'jornalista',
            ],
        ]);
    }
}
