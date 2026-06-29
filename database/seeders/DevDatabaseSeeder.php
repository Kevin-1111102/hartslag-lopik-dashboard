<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Uses the existing seeder logic.
        $this->call(DatabaseSeeder::class);
    }
}

