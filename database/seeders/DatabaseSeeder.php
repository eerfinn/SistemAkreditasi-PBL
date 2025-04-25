<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Levels harus di-seed terlebih dahulu karena User membutuhkan level_id
        $this->call([
            LevelSeeder::class,
            UserSeeder::class,
        ]);
    }
}
