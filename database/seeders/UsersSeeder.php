<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'user_id' => 4,
            'first_name' => 'Jan Vincent',
            'last_name' => 'Dominguez',
            'email' => 'adopter@gmail.com',
            'usertype' => 'user',
            'password' => '$2y$12$6VLuIaKZk5uaAN9b0skrseKw3GdJaPQZQ5McBzRP8O8OlFfBwwlKW',
            'phone_number' => '09760792254',
            'role' => 'adopter',
            'created_at' => '2025-06-16 10:21:33',
            'updated_at' => '2025-06-16 10:21:33',
        ]);

        DB::table('users')->insert([
            'user_id' => 5,
            'first_name' => 'JAN VINCENT',
            'last_name' => 'DOMINGUEZ',
            'email' => 'shelter@gmail.com',
            'usertype' => 'user',
            'password' => '$2y$12$sfdZ1NHAvifX2FYEnzFn8er5cqIyQgpFrj7r29iCFJfGaQ45fwy92',
            'phone_number' => '09760792254',
            'role' => 'shelter',
            'created_at' => '2025-06-16 10:28:34',
            'updated_at' => '2025-06-16 10:28:34',
        ]);

        DB::table('users')->insert([
            'user_id' => 6,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'usertype' => 'user',
            'password' => '$2y$12$8j35qrjp7qnoOO4A0HdEeeK2c9QtA90Q5HcSIoGV.VO3/m8d/BfJS',
            'phone_number' => '09123456789',
            'role' => 'admin',
            'created_at' => '2025-06-19 09:40:37',
            'updated_at' => '2025-06-19 09:40:37',
        ]);

    }
}
