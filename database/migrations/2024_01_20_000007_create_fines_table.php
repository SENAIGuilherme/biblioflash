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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('loan_id')->constrained('loans');
            $table->decimal('valor', 8, 2);
            $table->text('descricao');
            $table->enum('tipo', ['atraso', 'dano', 'perda'])->default('atraso');
            $table->timestamp('data_geracao')->useCurrent();
            $table->timestamp('data_vencimento')->nullable();
            $table->timestamp('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'paga', 'cancelada', 'vencida'])->default('pendente');
            $table->string('forma_pagamento', 50)->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('funcionario_id')->nullable()->constrained('users'); // Funcionário que gerou/recebeu
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['user_id', 'status']);
            $table->index('loan_id');
            $table->index('data_geracao');
            $table->index('data_vencimento');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};