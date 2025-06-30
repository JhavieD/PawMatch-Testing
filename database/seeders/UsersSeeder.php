<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shared\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '09123456789',
            'role' => 'admin',
            'usertype' => 'user',
        ]);

        // Create adopter user  
        User::create([
            'first_name' => 'Jan Vincent',
            'last_name' => 'Dominguez',
            'email' => 'adopter@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '09760792254',
            'role' => 'adopter',
            'usertype' => 'user',
        ]);

        // Create shelter user
        User::create([
            'first_name' => 'Shelter',
            'last_name' => 'Manager',
            'email' => 'shelter@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '09760792254',
            'role' => 'shelter',
            'usertype' => 'user',
        ]);

        // Create rescuer user
        User::create([
            'first_name' => 'Rescuer',
            'last_name' => 'Hero',
            'email' => 'rescuer@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '09987654321',
            'role' => 'rescuer',
            'usertype' => 'user',
        ]);
    }
}
