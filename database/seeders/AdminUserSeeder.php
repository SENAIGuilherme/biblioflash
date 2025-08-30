<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário admin já existe
        $adminUser = User::where('email', 'adm@adm.com')->first();
        
        if (!$adminUser) {
            User::create([
                'name' => 'Admin',
                'email' => 'adm@adm.com',
                'password' => Hash::make('adm'),
                'tipo' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]);
            
            echo "Usuário admin criado com sucesso!\n";
        } else {
            echo "Usuário admin já existe.\n";
        }
    }
}
