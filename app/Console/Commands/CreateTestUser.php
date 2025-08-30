<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test';
    protected $description = 'Create a test user for debugging login issues';

    public function handle()
    {
        // Check if test user already exists
        $existingUser = User::where('email', 'test@test.com')->first();
        
        if ($existingUser) {
            $this->info('Test user already exists. Updating password...');
            $existingUser->update([
                'password' => Hash::make('12345678')
            ]);
            $this->info('Test user password updated successfully!');
        } else {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@test.com',
                'password' => Hash::make('12345678'),
                'tipo' => 'cliente',
                'ativo' => true,
            ]);
            
            $this->info('Test user created successfully!');
        }
        
        $this->info('Email: test@test.com');
        $this->info('Password: 12345678');
        
        // Show all users count
        $userCount = User::count();
        $this->info("Total users in database: {$userCount}");
        
        return 0;
    }
}
