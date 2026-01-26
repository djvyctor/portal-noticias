<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// seeder principal que chama todos os outros seeders
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    // popula o banco de dados com dados iniciais
    public function run(): void
    {
        // cria usuario de teste
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // chama os seeders especificos
        $this->call([
            UserSeeder::class,      // cria usuarios
            CategorySeeder::class,   // cria categorias
        ]);
    }
}
