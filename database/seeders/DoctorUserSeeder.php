<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Support\Facades\Hash;

class DoctorUserSeeder extends Seeder
{
    public function run()
    {
        // Datos de los doctores
        $doctorsData = [
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.dr@medilink.com',
                'phone' => '+1234567890',
                'address' => 'Calle Principal 123, Ciudad Médica',
                'id_number' => 'DR001',
                'speciality' => 'Cardiología',
                'license' => 'MED-12345',
                'biography' => 'Especialista en cardiología con más de 10 años de experiencia en enfermedades del corazón.'
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.dr@medilink.com',
                'phone' => '+1234567891',
                'address' => 'Avenida Salud 456, Zona Hospitalaria',
                'id_number' => 'DR002',
                'speciality' => 'Pediatría',
                'license' => 'MED-67890',
                'biography' => 'Pediatra dedicada al cuidado integral de niños y adolescentes.'
            ],
            [
                'name' => 'Luis García',
                'email' => 'luis.dr@medilink.com',
                'phone' => '+1234567892',
                'address' => 'Boulevard Médico 789, Centro Clínico',
                'id_number' => 'DR003',
                'speciality' => 'Ginecología',
                'license' => 'MED-24680',
                'biography' => 'Ginecólogo experto en salud femenina y reproductiva.'
            ],
            [
                'name' => 'María López',
                'email' => 'maria.dr@medilink.com',
                'phone' => '+1234567893',
                'address' => 'Plaza Sanitaria 321, Sector Salud',
                'id_number' => 'DR004',
                'speciality' => 'Dermatología',
                'license' => 'MED-13579',
                'biography' => 'Dermatóloga especializada en tratamientos cosméticos y médicos.'
            ],
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.dr@medilink.com',
                'phone' => '+1234567894',
                'address' => 'Carrera Bienestar 654, Área Médica',
                'id_number' => 'DR005',
                'speciality' => 'Medicina General',
                'license' => 'MED-97531',
                'biography' => 'Médico general con enfoque en medicina preventiva y familiar.'
            ]
        ];

        foreach ($doctorsData as $data) {
            // Crear o encontrar especialidad
            $speciality = Speciality::firstOrCreate(['name' => $data['speciality']]);

            // Crear usuario
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'id_number' => $data['id_number'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now()
                ]
            );

            // Asignar rol de doctor
            if (!$user->hasRole('doctor')) {
                $user->assignRole('doctor');
            }

            // Crear doctor
            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'speciality_id' => $speciality->id,
                    'medical_license_number' => $data['license'],
                    'biography' => $data['biography']
                ]
            );
        }

        $this->command->info('Se han creado 5 doctores con sus usuarios exitosamente.');
        $this->command->info('Emails: carlos.dr@medilink.com, ana.dr@medilink.com, luis.dr@medilink.com, maria.dr@medilink.com, juan.dr@medilink.com');
        $this->command->info('Contraseña: password123');
    }
}
