<?php

namespace App\Services;

use Exception;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WhatsAppService
{
    private string $nodeScriptPath;
    private string $whatsappStatePath;

    public function __construct()
    {
        $this->nodeScriptPath = base_path('resources/js/whatsapp-real.cjs');
        $this->whatsappStatePath = storage_path('app/whatsapp-session');
    }

    /**
     * Enviar mensaje de WhatsApp
     */
    public function sendMessage(string $phone, string $message): bool
    {
        try {
            // Limpiar número de teléfono (solo dígitos)
            $cleanPhone = $this->cleanPhoneNumber($phone);
            
            // Formatear para WhatsApp (con código de país si no tiene)
            $whatsappPhone = $this->formatPhoneForWhatsApp($cleanPhone);
            
            // Preparar datos para el script de Node
            $data = [
                'phone' => $whatsappPhone,
                'message' => $message,
                'sessionPath' => $this->whatsappStatePath
            ];

            // Ejecutar script de Node.js
            $result = $this->executeNodeScript($data);
            
            return $this->parseResult($result);
            
        } catch (Exception $e) {
            \Log::error('Error sending WhatsApp message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de confirmación de cita
     */
    public function sendAppointmentConfirmation($appointment): bool
    {
        $message = $this->buildConfirmationMessage($appointment);
        $phone = $appointment->patient->user->phone;
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Enviar recordatorio de cita
     */
    public function sendAppointmentReminder($appointment): bool
    {
        $message = $this->buildReminderMessage($appointment);
        $phone = $appointment->patient->user->phone;
        
        return $this->sendMessage($phone, $message);
    }

    /**
     * Verificar si WhatsApp está conectado
     */
    public function isConnected(): bool
    {
        try {
            $result = $this->executeNodeScript([
                'action' => 'checkConnection',
                'sessionPath' => $this->whatsappStatePath
            ]);
            
            return $this->parseResult($result);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener QR code para conexión
     */
    public function getQRCode(): ?string
    {
        try {
            $result = $this->executeNodeScript([
                'action' => 'getQR',
                'sessionPath' => $this->whatsappStatePath
            ]);
            
            $parsed = json_decode($result, true);
            return $parsed['qr'] ?? null;
        } catch (Exception $e) {
            \Log::error('Error getting QR code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Limpiar número de teléfono
     */
    private function cleanPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Formatear número para WhatsApp
     */
    private function formatPhoneForWhatsApp(string $phone): string
    {
        // Si no tiene código de país, asumir México (52)
        if (strlen($phone) === 10) {
            return '52' . $phone;
        }
        
        return $phone;
    }

    /**
     * Construir mensaje de confirmación
     */
    private function buildConfirmationMessage($appointment): string
    {
        // Convertir a Carbon si no lo son
        $date = is_string($appointment->date) ? \Carbon\Carbon::parse($appointment->date) : $appointment->date;
        $time = is_string($appointment->start_time) ? \Carbon\Carbon::parse($appointment->start_time) : $appointment->start_time;
        
        $formattedDate = $date->format('d/m/Y');
        $formattedTime = $time->format('H:i');
        $doctor = $appointment->doctor->user->name;
        
        return "🏥 *CONFIRMACIÓN DE CITA*\n\n" .
               "✅ *Cita Confirmada*\n" .
               "📅 *Fecha:* $formattedDate\n" .
               "⏰ *Hora:* $formattedTime\n" .
               "👨‍⚕️ *Doctor:* Dr. $doctor\n" .
               "📍 *Ubicación:* Clínica Médica\n\n" .
               "Por favor arrive 10 minutos antes.\n\n" .
               "📞 Para cambios: 555-123-4567";
    }

    /**
     * Construir mensaje de recordatorio
     */
    private function buildReminderMessage($appointment): string
    {
        // Convertir a Carbon si no lo son
        $date = is_string($appointment->date) ? \Carbon\Carbon::parse($appointment->date) : $appointment->date;
        $time = is_string($appointment->start_time) ? \Carbon\Carbon::parse($appointment->start_time) : $appointment->start_time;
        
        $formattedDate = $date->format('d/m/Y');
        $formattedTime = $time->format('H:i');
        $doctor = $appointment->doctor->user->name;
        
        return "⏰ *RECORDATORIO DE CITA*\n\n" .
               "📅 *Mañana tienes cita*\n" .
               "🗓️ *Fecha:* $formattedDate\n" .
               "⏰ *Hora:* $formattedTime\n" .
               "👨‍⚕️ *Doctor:* Dr. $doctor\n" .
               "📍 *Ubicación:* Clínica Médica\n\n" .
               "⚠️ *Importante:* Por favor arrive 10 minutos antes.\n\n" .
               "Si no puedes asistir, llama al 555-123-4567";
    }

    /**
     * Ejecutar script de Node.js
     */
    private function executeNodeScript(array $data): string
    {
        $jsonData = json_encode($data);
        
        $process = new Process(['node', $this->nodeScriptPath], null, [
            'WHATSAPP_DATA' => $jsonData
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return $process->getOutput();
    }

    /**
     * Parsear resultado del script
     */
    private function parseResult(string $result): bool
    {
        $parsed = json_decode($result, true);
        return $parsed['success'] ?? false;
    }
}
