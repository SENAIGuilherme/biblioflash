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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique(); // Chave da configuração
            $table->text('value')->nullable(); // Valor da configuração
            $table->string('type', 50)->default('string'); // Tipo: string, integer, boolean, json, etc.
            $table->text('description')->nullable(); // Descrição da configuração
            $table->string('group', 50)->default('general'); // Grupo da configuração
            $table->boolean('is_public')->default(false); // Se pode ser acessada publicamente
            $table->timestamps();
            
            // Índices para otimização
            $table->index('group');
            $table->index('is_public');
        });
        
        // Inserir configurações padrão
        DB::table('system_settings')->insert([
            [
                'key' => 'loan_duration_days',
                'value' => '14',
                'type' => 'integer',
                'description' => 'Duração padrão do empréstimo em dias',
                'group' => 'loans',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_renewals',
                'value' => '2',
                'type' => 'integer',
                'description' => 'Número máximo de renovações por empréstimo',
                'group' => 'loans',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'fine_per_day',
                'value' => '2.00',
                'type' => 'decimal',
                'description' => 'Valor da multa por dia de atraso',
                'group' => 'fines',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'reservation_expiry_hours',
                'value' => '48',
                'type' => 'integer',
                'description' => 'Horas para expiração da reserva',
                'group' => 'reservations',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_books_per_user',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Número máximo de livros por usuário',
                'group' => 'loans',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};