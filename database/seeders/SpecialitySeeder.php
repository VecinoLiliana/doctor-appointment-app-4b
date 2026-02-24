<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'Cardiología',
            'Pediatría',
            'Medicina General',
            'Ginecología',
            'Oftalmología',
            'Neurología',
            'Endocrinología',
            'Reumatología',
            'Veterinaria'
        ];

        foreach ($specialities as $speciality) {
            \App\Models\Speciality::firstOrCreate([
                'name' => $speciality,
            ]);
        }
    }
}
