<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a RoleSeeder
        $this->call([
            SpecialitySeeder::class,
            BloodTypeSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);


    }
}
