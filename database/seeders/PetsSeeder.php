<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pets')->insert([
            'pet_id' => 1,
            'shelter_id' => 1,
            'rescuer_id' => null,
            'name' => 'Ester',
            'species' => 'cat',
            'breed' => 'Tabby',
            'age' => 1,
            'gender' => 'male',
            'size' => 'small',
            'medical_history' => null,
            'adoption_status' => 'available',
            'behavior' => 'Independent',
            'daily_activity' => 'Low',
            'eating_habits' => null,
            'special_needs' => 'No',
            'compatibility' => 'No',
            'description' => 'Fat ass cat',
            'created_at' => '2025-06-16 10:33:51',
            'updated_at' => '2025-06-16 10:33:51',
        ]);

        DB::table('pets')->insert([
            'pet_id' => 2,
            'shelter_id' => 1,
            'rescuer_id' => null,
            'name' => 'Sky',
            'species' => 'dog',
            'breed' => 'Shipoodle',
            'age' => 2,
            'gender' => 'female',
            'size' => 'small',
            'medical_history' => null,
            'adoption_status' => 'available',
            'behavior' => 'Playful and Energetic',
            'daily_activity' => 'Low',
            'eating_habits' => null,
            'special_needs' => 'Yes',
            'compatibility' => 'Yes',
            'description' => 'FUCKING DOG',
            'created_at' => '2025-06-16 10:34:32',
            'updated_at' => '2025-06-16 10:34:32',
        ]);

        DB::table('pets')->insert([
            'pet_id' => 3,
            'shelter_id' => 1,
            'rescuer_id' => null,
            'name' => 'Presto',
            'species' => 'dog',
            'breed' => 'Aspin',
            'age' => 12,
            'gender' => 'male',
            'size' => 'small',
            'medical_history' => null,
            'adoption_status' => 'available',
            'behavior' => 'Playful and Energetic',
            'daily_activity' => 'Low',
            'eating_habits' => null,
            'special_needs' => 'Yes',
            'compatibility' => 'Yes',
            'description' => 'Small fat dog',
            'created_at' => '2025-06-16 10:34:59',
            'updated_at' => '2025-06-16 10:34:59',
        ]);

    }
}
