<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shared\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password'),
                'phone_number' => '09062061730',
                'role' => 'admin',
            ]
        );
    }
} 