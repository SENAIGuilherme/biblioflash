<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckTestUser extends Command
{
    protected $signature = 'user:check-test';
    protected $description = 'Check test user details and verify password';

    public function handle()
    {
        $user = User::where('email', 'test@test.com')->first();
        
        if (!$user) {
            $this->error('Test user not found!');
            return 1;
        }
        
        $this->info('User Details:');
        $this->info('ID: ' . $user->id);
        $this->info('Name: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('Tipo: ' . $user->tipo);
        $this->info('Ativo: ' . ($user->ativo ? 'Yes' : 'No'));
        $this->info('Created: ' . $user->created_at);
        $this->info('Updated: ' . $user->updated_at);
        
        // Test password verification
        $testPassword = '12345678';
        $passwordCheck = Hash::check($testPassword, $user->password);
        $this->info('Password check for "' . $testPassword . '": ' . ($passwordCheck ? 'VALID' : 'INVALID'));
        
        // Show password hash
        $this->info('Password hash: ' . $user->password);
        
        return 0;
    }
}
