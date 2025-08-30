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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->string('autor', 255);
            $table->string('editora', 255)->nullable();
            $table->string('isbn', 20)->unique();
            $table->string('rfid_tag', 50)->nullable()->unique(); // Para sistema RFID
            $table->foreignId('category_id')->constrained('categories');
            $table->text('descricao')->nullable();
            $table->string('foto')->nullable(); // Caminho da imagem da capa
            $table->integer('paginas')->nullable();
            $table->year('ano_publicacao')->nullable();
            $table->string('idioma', 10)->default('pt-BR');
            $table->decimal('preco', 8, 2)->nullable();
            $table->integer('quantidade_total')->default(1);
            $table->integer('quantidade_disponivel')->default(1);
            $table->string('localizacao', 100)->nullable(); // Localização física na biblioteca
            $table->enum('status', ['disponivel', 'indisponivel', 'manutencao'])->default('disponivel');
            $table->integer('total_emprestimos')->default(0);
            $table->decimal('avaliacao_media', 3, 2)->default(0.00);
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['titulo', 'autor']);
            $table->index('isbn');
            $table->index('rfid_tag');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};