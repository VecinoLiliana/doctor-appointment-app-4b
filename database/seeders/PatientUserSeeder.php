<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientUserSeeder extends Seeder
{
    public function run()
    {
        // Datos de los pacientes
        $patientsData = [
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.paciente@medilink.com',
                'phone' => '+9876543210',
                'address' => 'Calle Paciente 123, Zona Residencial',
                'id_number' => 'PAC001',
                'allergies' => 'Penicilina, Polen',
                'chronic_conditions' => 'Hipertensión leve',
                'emergency_contact_phone' => '+9876543299',
                'emergency_contact_relationship' => 'Esposa'
            ],
            [
                'name' => 'Carmen Torres',
                'email' => 'carmen.paciente@medilink.com',
                'phone' => '+9876543211',
                'address' => 'Avenida Salud 456, Barrio Médico',
                'id_number' => 'PAC002',
                'allergies' => 'Ninguna conocida',
                'chronic_conditions' => 'Asma controlada',
                'emergency_contact_phone' => '+9876543298',
                'emergency_contact_relationship' => 'Hermano'
            ],
            [
                'name' => 'Miguel Ángel',
                'email' => 'miguel.paciente@medilink.com',
                'phone' => '+9876543212',
                'address' => 'Boulevard Bienestar 789, Sector Salud',
                'id_number' => 'PAC003',
                'allergies' => 'Mariscos',
                'chronic_conditions' => 'Diabetes tipo 2',
                'emergency_contact_phone' => '+9876543297',
                'emergency_contact_relationship' => 'Hija'
            ],
            [
                'name' => 'Laura Gómez',
                'email' => 'laura.paciente@medilink.com',
                'phone' => '+9876543213',
                'address' => 'Plaza Sanitaria 321, Área Clínica',
                'id_number' => 'PAC004',
                'allergies' => 'Látex',
                'chronic_conditions' => 'Migrañas ocasionales',
                'emergency_contact_phone' => '+9876543296',
                'emergency_contact_relationship' => 'Padre'
            ],
            [
                'name' => 'Diego Hernández',
                'email' => 'diego.paciente@medilink.com',
                'phone' => '+9876543214',
                'address' => 'Carrera Recuperación 654, Zona Hospitalaria',
                'id_number' => 'PAC005',
                'allergies' => 'Ninguna',
                'chronic_conditions' => 'Ninguna',
                'emergency_contact_phone' => '+9876543295',
                'emergency_contact_relationship' => 'Madre'
            ]
        ];

        foreach ($patientsData as $data) {
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

            // Asignar rol de patient
            if (!$user->hasRole('patient')) {
                $user->assignRole('patient');
            }

            // Crear paciente
            Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'allergies' => $data['allergies'],
                    'chronic_conditions' => $data['chronic_conditions'],
                    'emergency_contact_phone' => $data['emergency_contact_phone'],
                    'emergency_contact_relationship' => $data['emergency_contact_relationship']
                ]
            );
        }

        $this->command->info('Se han creado 5 pacientes con sus usuarios exitosamente.');
        $this->command->info('Emails: roberto.paciente@medilink.com, carmen.paciente@medilink.com, miguel.paciente@medilink.com, laura.paciente@medilink.com, diego.paciente@medilink.com');
        $this->command->info('Contraseña: password123');
    }
}
