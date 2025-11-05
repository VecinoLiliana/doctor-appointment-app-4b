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
            RoleSeeder::class
        ]);

        // Crear usuario de prueba cada que se ejecuten migraciones
        //php artisan migrate:fresh --seed (comando que limpia toda la base de datos)
        User::factory()->create([
            'name' => 'Lili Vecino',
            'email' => 'lili@example.com',
            'password' => bcrypt('123456789') //bcrypt encripta datos
        ]);
    }
}
