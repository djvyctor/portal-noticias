<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// adiciona colunas author_name e rejection_reason na tabela news
return new class extends Migration
{
    // adiciona colunas
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('user_id'); // nome do autor -> salvo para nao perder se usuario for deletado
            $table->text('rejection_reason')->nullable()->after('status'); // motivo da rejeicao -> se status for rejected
        });
    }

    // remove colunas
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'rejection_reason']);
        });
    }
};