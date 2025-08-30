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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');
            $table->timestamp('data_reserva')->useCurrent();
            $table->timestamp('data_expiracao')->nullable(); // Reserva expira em X dias
            $table->enum('status', ['ativa', 'cancelada', 'expirada', 'retirada'])->default('ativa');
            $table->text('observacoes')->nullable();
            $table->timestamp('data_cancelamento')->nullable();
            $table->string('motivo_cancelamento')->nullable();
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('data_reserva');
            $table->index('data_expiracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};