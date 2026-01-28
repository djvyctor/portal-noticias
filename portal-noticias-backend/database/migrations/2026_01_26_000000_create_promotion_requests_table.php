<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// cria tabela de solicitacoes de promocao de jornalista para editor
return new class extends Migration
{
    // cria tabela promotion_requests
    public function up(): void
    {
        Schema::create('promotion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // jornalista que solicitou
            $table->text('message'); // mensagem da solicitacao
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // status da solicitacao
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // editor/admin que aprovou/rejeitou
            $table->text('rejection_reason')->nullable(); // motivo da rejeicao (se rejeitada)
            $table->timestamp('reviewed_at')->nullable(); // data da revisao
            $table->timestamps(); // created_at e updated_at
        });
    }

    // remove tabela promotion_requests
    public function down(): void
    {
        Schema::dropIfExists('promotion_requests');
    }
};
