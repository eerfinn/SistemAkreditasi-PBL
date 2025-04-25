<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Level;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin Level
        $adminLevel = Level::create([
            'name' => 'Administrator',
            'description' => 'Super Admin with full access',
            'status' => true
        ]);

        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'level_id' => $adminLevel->id
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: admin123');
    }
} 