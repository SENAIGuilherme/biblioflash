<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users'); // Usuário que executou a ação
            $table->string('action', 100); // Tipo de ação (login, logout, emprestimo, devolucao, etc.)
            $table->string('model_type', 100)->nullable(); // Tipo do modelo afetado
            $table->unsignedBigInteger('model_id')->nullable(); // ID do modelo afetado
            $table->json('old_values')->nullable(); // Valores antigos (para updates)
            $table->json('new_values')->nullable(); // Valores novos
            $table->text('description')->nullable(); // Descrição da ação
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Índices para otimização
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};