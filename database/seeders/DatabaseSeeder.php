<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Production-safe seed data. Create the first admin with: php artisan app:create-admin */
    public function run(): void
    {
        $this->call(CategorySeeder::class);
    }
}
