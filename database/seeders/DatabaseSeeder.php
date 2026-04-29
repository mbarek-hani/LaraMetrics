<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,     // 1. Users first 👤
            SiteSeeder::class,     // 2. Sites second 🌐
            VisiteSeeder::class,   // 3. Visits third 📊
            EvenementSeeder::class, // 4. Events last 🎯
        ]);
    }
}