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
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('loan_id')->nullable()->constrained('loans'); // Avaliação baseada em empréstimo
            $table->integer('avaliacao')->unsigned(); // 1 a 5 estrelas
            $table->text('comentario')->nullable();
            $table->boolean('recomenda')->default(true);
            $table->boolean('aprovado')->default(false); // Moderação de comentários
            $table->timestamp('data_aprovacao')->nullable();
            $table->foreignId('moderador_id')->nullable()->constrained('users');
            $table->timestamps();
            
            // Constraint será validada no model/controller
            
            // Índices para otimização
            $table->unique(['user_id', 'book_id']); // Um usuário só pode avaliar um livro uma vez
            $table->index('book_id');
            $table->index('avaliacao');
            $table->index('aprovado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};