<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SheltersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shelters')->insert([
            'shelter_id' => 1,
            'user_id' => 5,
            'shelter_name' => 'Ester\'s House',
            'location' => 'QC',
            'contact_info' => '09760792254',
            'verified' => 0,
            'verified_by' => null,
            'verified_at' => null,
            'avg_adopter_rating' => null,
            'created_at' => '2025-06-16 10:28:34',
            'updated_at' => '2025-06-16 10:28:34',
        ]);

    }
}
