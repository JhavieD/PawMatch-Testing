<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdoptersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('adopters')->insert([
            'adopter_id' => 1,
            'user_id' => 4,
            'address' => 'Block 6 Lot 3 Friendship HOA street Sitio Hinapao',
            'adoption_status' => 'pending',
            'created_at' => '2025-06-16 10:21:38',
            'updated_at' => '2025-06-16 10:21:38',
        ]);

    }
}
