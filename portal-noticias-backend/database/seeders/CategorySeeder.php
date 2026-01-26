<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

// cria categorias iniciais do sistema
class CategorySeeder extends Seeder
{
    // cria categorias padrao
    public function run(): void
    {
        $categories = [
            'Esportes',
            'Política',
            'Tecnologia',
            'Economia',
            'Mundo',
        ];

        $data = array_map(function ($name) {
            return [
                'name' => $name,
                'slug' => Str::slug($name),
            ];
        }, $categories);

        Category::insert($data);
    }
}
