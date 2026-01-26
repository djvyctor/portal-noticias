<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// adiciona coluna role na tabela users
return new class extends Migration
{
    // adiciona coluna role
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'editor', 'jornalista'])
                ->default('jornalista')
                ->after('email');
        });
    }

    // remove coluna role
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
