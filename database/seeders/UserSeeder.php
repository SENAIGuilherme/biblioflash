<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin de teste
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'tipo' => 'admin',
            'ativo' => true,
            'ultimo_acesso' => now(),
        ]);

        // Criar usuário cliente de teste
        User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => Hash::make('123456789'),
            'cpf' => '12345678901',
            'telefone' => '(11) 99999-9999',
            'tipo' => 'cliente',
            'ativo' => true,
            'ultimo_acesso' => now(),
        ]);
    }
}
