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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations'); // Pode vir de uma reserva
            $table->timestamp('data_emprestimo')->useCurrent();
            $table->timestamp('data_prevista_devolucao');
            $table->timestamp('data_devolucao')->nullable();
            $table->enum('status', ['ativo', 'devolvido', 'atrasado', 'perdido'])->default('ativo');
            $table->text('observacoes_emprestimo')->nullable();
            $table->text('observacoes_devolucao')->nullable();
            $table->foreignId('funcionario_emprestimo_id')->nullable()->constrained('users'); // Funcionário que fez o empréstimo
            $table->foreignId('funcionario_devolucao_id')->nullable()->constrained('users'); // Funcionário que recebeu a devolução
            $table->boolean('renovado')->default(false);
            $table->integer('numero_renovacoes')->default(0);
            $table->decimal('multa_valor', 8, 2)->default(0.00);
            $table->boolean('multa_paga')->default(false);
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('data_emprestimo');
            $table->index('data_prevista_devolucao');
            $table->index('data_devolucao');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};