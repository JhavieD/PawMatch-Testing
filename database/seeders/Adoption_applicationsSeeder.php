<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Adoption_applicationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('adoption_applications')->insert([
            'application_id' => 1,
            'adopter_id' => 1,
            'pet_id' => 1,
            'shelter_id' => 1,
            'rescuer_id' => null,
            'reason_for_adoption' => 'I like',
            'living_environment' => null,
            'experience_with_pets' => 'A good one',
            'household_members' => 4,
            'allergies' => 0,
            'has_other_pets' => 1,
            'other_pets_details' => 'A dog and a dog',
            'can_provide_vet_care' => 1,
            'status' => 'pending',
            'rejection_reason' => null,
            'submitted_at' => '2025-06-16 14:59:06',
            'created_at' => '2025-06-16 14:59:06',
            'updated_at' => '2025-06-16 14:59:06',
        ]);

        DB::table('adoption_applications')->insert([
            'application_id' => 2,
            'adopter_id' => 1,
            'pet_id' => 2,
            'shelter_id' => 1,
            'rescuer_id' => null,
            'reason_for_adoption' => 'yes',
            'living_environment' => null,
            'experience_with_pets' => 'yes',
            'household_members' => 3,
            'allergies' => 0,
            'has_other_pets' => 0,
            'other_pets_details' => null,
            'can_provide_vet_care' => 0,
            'status' => 'pending',
            'rejection_reason' => null,
            'submitted_at' => '2025-06-17 06:00:43',
            'created_at' => '2025-06-17 06:00:43',
            'updated_at' => '2025-06-17 06:00:43',
        ]);

    }
}
