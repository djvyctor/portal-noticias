<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// cria tabela de noticias
return new class extends Migration
{
    // cria tabela news
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // titulo da noticia
            $table->string('slug')->unique(); // URL amigavel
            $table->text('content'); // conteudo da noticia
            $table->string('image_path')->nullable(); // caminho da imagem
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // status inicial
            $table->boolean('is_featured')->default(false); // se esta em destaque
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // autor (se deletar usuario, deleta noticias)
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // categoria (se deletar categoria, deleta noticias)
            $table->timestamps(); // created_at e updated_at
        });
    }

    // remove tabela news
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
