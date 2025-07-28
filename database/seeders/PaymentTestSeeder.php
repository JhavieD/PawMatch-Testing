<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shelter\Shelter;
use App\Models\Rescuer\Rescuer;

class PaymentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set adoption fees for existing shelters
        Shelter::all()->each(function ($shelter) {
            $shelter->update([
                'adoption_fee' => 5000.00,
                'bank_name' => 'Test Bank',
                'bank_account_number' => '1234567890',
                'bank_account_name' => $shelter->shelter_name
            ]);
        });

        // Set adoption fees for existing rescuers
        Rescuer::all()->each(function ($rescuer) {
            $rescuer->update([
                'adoption_fee' => 3000.00,
                'bank_name' => 'Test Bank',
                'bank_account_number' => '0987654321',
                'bank_account_name' => $rescuer->organization_name
            ]);
        });

        echo "Payment test data set up successfully!\n";
        echo "Shelters: " . Shelter::count() . " (₱5,000 adoption fee each)\n";
        echo "Rescuers: " . Rescuer::count() . " (₱3,000 adoption fee each)\n";
    }
} 