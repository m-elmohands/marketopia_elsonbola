<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BusinessSettingSeeder::class,
            ProductSeeder::class,
            PageSeeder::class,
            WebsiteMediaSeeder::class,
        ]);
    }
}
