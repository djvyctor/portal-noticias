<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// factory para criar usuarios fake
class UserFactory extends Factory
{
    // senha padrao usada pelo factory
    protected static ?string $password;

    // define os dados padrao para criar um usuario
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_JORNALISTA, // role padrao
            'remember_token' => Str::random(10),
        ];
    }

    // cria usuario com email nao verificado
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // cria usuario admin
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    // cria usuario editor
    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_EDITOR,
        ]);
    }

    // cria usuario jornalista
    public function jornalista(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_JORNALISTA,
        ]);
    }
}
