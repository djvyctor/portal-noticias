<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// adiciona coluna published_at na tabela news
return new class extends Migration
{
    // adiciona coluna published_at
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('status');
        });
    }

    // remove coluna published_at
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};