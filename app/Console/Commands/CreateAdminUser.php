<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin';
    protected $description = 'Create or update admin user with admin@admin.com and password admin123';

    public function handle()
    {
        // Check if admin user already exists
        $existingUser = User::where('email', 'admin@admin.com')->first();
        
        if ($existingUser) {
            $this->info('Admin user already exists. Updating password...');
            $existingUser->update([
                'password' => Hash::make('admin123'),
                'name' => 'Administrador',
                'tipo' => 'admin',
                'ativo' => true
            ]);
            $this->info('Admin user updated successfully!');
        } else {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'tipo' => 'admin',
                'ativo' => true,
                'ultimo_acesso' => now(),
            ]);
            
            $this->info('Admin user created successfully!');
        }
        
        $this->info('Email: admin@admin.com');
        $this->info('Password: admin123');
        
        return 0;
    }
}