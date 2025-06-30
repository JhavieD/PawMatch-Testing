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
            'password' => '$2y$12$6VLuIaKZk5uaAN9b0skrseKw3GdJaPQZQ5McBzRP8O8OlFfBwwlKW',
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
            'password' => '$2y$12$sfdZ1NHAvifX2FYEnzFn8er5cqIyQgpFrj7r29iCFJfGaQ45fwy92',
            'password' => bcrypt('password'),
            'phone_number' => '09760792254',
            'role' => 'shelter',
            'usertype' => 'user',
        ]);

        DB::table('users')->insert([
            'user_id' => 6,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'password' => '$2y$12$8j35qrjp7qnoOO4A0HdEeeK2c9QtA90Q5HcSIoGV.VO3/m8d/BfJS',
            'phone_number' => '09123456789',
            'role' => 'admin',
            'created_at' => '2025-06-19 09:40:37',
            'updated_at' => '2025-06-19 09:40:37',
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
