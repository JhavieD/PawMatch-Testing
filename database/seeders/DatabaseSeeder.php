<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            SheltersSeeder::class,
            AdoptersSeeder::class,
            PetsSeeder::class,
            Adoption_applicationsSeeder::class,
        ]);
    }
}
