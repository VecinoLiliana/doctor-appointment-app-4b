<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios de citas por WhatsApp para mañana';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando citas para mañana...');
        
        // Obtener citas para mañana (excluyendo canceladas)
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('date', $tomorrow)
            ->where('status', '!=', 4) // Excluir canceladas
            ->where('status', '!=', 5) // Excluir reprogramadas
            ->get();

        $this->info("Se encontraron {$appointments->count()} citas para mañana");

        if ($appointments->isEmpty()) {
            $this->info('No hay citas para enviar recordatorios.');
            return 0;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($appointments as $appointment) {
            try {
                // Verificar que el paciente tenga teléfono
                if (!$appointment->patient->user->phone) {
                    $this->warn("Paciente {$appointment->patient->user->name} no tiene teléfono");
                    $failedCount++;
                    continue;
                }

                // Enviar recordatorio
                SendWhatsAppMessage::dispatch($appointment, 'reminder');
                
                $dateFormatted = \Carbon\Carbon::parse($appointment->date)->format('d/m/Y');
                $timeFormatted = \Carbon\Carbon::parse($appointment->start_time)->format('H:i');
                $this->info("✅ Recordatorio programado para: {$appointment->patient->user->name} - {$dateFormatted} {$timeFormatted}");
                $sentCount++;

            } catch (\Exception $e) {
                $this->error("❌ Error al programar recordatorio para cita {$appointment->id}: " . $e->getMessage());
                $failedCount++;
                Log::error("Error scheduling reminder", [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n--- Resumen ---");
        $this->info("✅ Recordatorios programados: {$sentCount}");
        $this->info("❌ Fallidos: {$failedCount}");
        $this->info("Total procesados: " . ($sentCount + $failedCount));

        // Log general
        Log::info("Appointment reminders processed", [
            'date' => $tomorrow,
            'total_appointments' => $appointments->count(),
            'sent' => $sentCount,
            'failed' => $failedCount
        ]);

        return 0;
    }
}
