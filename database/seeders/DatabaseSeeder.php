<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário administrador padrão
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@biblioflash.com',
            'cpf' => '12345678901',
            'telefone' => '(11) 99999-9999',
            'tipo' => 'admin',
            'ativo' => true
        ]);

        // Criar usuário cliente de teste
        User::factory()->create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'cpf' => '98765432100',
            'telefone' => '(11) 88888-8888',
            'tipo' => 'cliente',
            'ativo' => true
        ]);

        // Executar seeders específicos
        $this->call([
            CategoriesSeeder::class,
            BooksSeeder::class,
        ]);
    }
}
