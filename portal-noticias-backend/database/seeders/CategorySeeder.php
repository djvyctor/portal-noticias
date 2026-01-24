<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Esportes',
                'slug' => Str::slug('Esportes'),
            ],
            [
                'name' => 'Política',
                'slug' => Str::slug('Política'),
            ],
            [
                'name' => 'Tecnologia',
                'slug' => Str::slug('Tecnologia'),
            ],
            [
                'name' => 'Economia',
                'slug' => Str::slug('Economia'),
            ],
            [
                'name' => 'Mundo',
                'slug' => Str::slug('Mundo'),
            ],
        ]);
    }
}
