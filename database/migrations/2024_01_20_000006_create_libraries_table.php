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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->string('endereco', 500);
            $table->string('cidade', 100);
            $table->string('estado', 2);
            $table->string('cep', 10);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->json('horario_funcionamento')->nullable(); // JSON com horários por dia da semana
            $table->json('temas')->nullable(); // JSON com temas/especialidades da biblioteca
            $table->string('responsavel', 255)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['cidade', 'estado']);
            $table->index(['latitude', 'longitude']);
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};