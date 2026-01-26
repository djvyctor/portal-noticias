<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// cria tabela de categorias
return new class extends Migration
{
    // cria tabela categories
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nome da categoria
            $table->string('slug')->unique(); // URL legivel
            $table->timestamps(); // created_at e updated_at
        });
    }

    // remove tabela categories
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
