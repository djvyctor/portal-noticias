<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar registros existentes de 'draft' para 'pending'
        DB::table('news')->where('status', 'draft')->update(['status' => 'pending']);
        DB::table('news')->where('status', 'archived')->update(['status' => 'rejected']);
        
        // Remover a coluna antiga
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Adicionar a coluna nova com os valores corretos
        Schema::table('news', function (Blueprint $table) {
            $table->enum('status', ['pending', 'published', 'rejected'])
                ->default('pending')
                ->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
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