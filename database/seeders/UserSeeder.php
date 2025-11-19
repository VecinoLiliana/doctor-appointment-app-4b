<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario de prueba cada que se ejecuten migraciones
        //php artisan migrate:fresh --seed (comando que limpia toda la base de datos)
        User::factory()->create([
            'name' => 'Lili Vecino',
            'email' => 'lili@example.com',
            'password' => bcrypt('123456789'), //bcrypt encripta datos
            'id_number' => '123456789',
            'phone' => '123456789',
            'address' => 'Calle 123, Colonia 526',
        ])->assignRole('Doctor');
    }
}
