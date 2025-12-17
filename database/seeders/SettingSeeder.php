<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'show_clock' => true,
            'image_duration' => 10,
        ]);
    }
}
