<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // force redeploy
            UsersSeeder::class,
            SheltersSeeder::class,
            AdoptersSeeder::class,
            PetsSeeder::class,
            Adoption_applicationsSeeder::class,
            \Database\Seeders\SettingsSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
