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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->nullable()->unique()->after('email');
            $table->string('telefone')->nullable()->after('cpf');
            $table->string('foto')->nullable()->after('telefone');
            $table->enum('tipo', ['cliente', 'admin', 'funcionario'])->default('cliente')->after('foto');
            $table->boolean('ativo')->default(true)->after('tipo');
            $table->timestamp('ultimo_acesso')->nullable()->after('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cpf',
                'telefone', 
                'foto',
                'tipo',
                'ativo',
                'ultimo_acesso'
            ]);
        });
    }
};