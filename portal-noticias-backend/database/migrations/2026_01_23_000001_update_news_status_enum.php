<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// atualiza enum de status da tabela news
// muda de draft, published, archived
// para pending, published, rejected
return new class extends Migration
{
    // atualiza status
    public function up(): void
    {
        // atualiza registros existentes antes de mudar o enum
        DB::table('news')->where('status', 'draft')->update(['status' => 'pending']);
        DB::table('news')->where('status', 'archived')->update(['status' => 'rejected']);
        
        // remove coluna antiga
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // adiciona coluna nova com valores corretos
        Schema::table('news', function (Blueprint $table) {
            $table->enum('status', ['pending', 'published', 'rejected'])
                ->default('pending')
                ->after('image_path');
        });
    }

    // reverte mudanca
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('news', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft')
                ->after('image_path');
        });
    }
};