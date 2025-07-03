<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'PawMatch', 'type' => 'string'],
            ['key' => 'contact_email', 'value' => 'contact@pawmatch.com', 'type' => 'string'],
            ['key' => 'email_notifications', 'value' => '1', 'type' => 'boolean'],
        ];
        foreach ($defaults as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
} 