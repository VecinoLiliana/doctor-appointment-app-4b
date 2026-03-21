<?php

namespace App\Services;

use Exception;
use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $twilioNumber;
    protected $accountSid;
    protected $authToken;

    public function __construct()
    {
        // Configuración de Twilio (debes configurar estas variables en .env)
        $this->accountSid = config('services.twilio.account_sid');
        $this->authToken = config('services.twilio.auth_token');
        $this->twilioNumber = config('services.twilio.phone_number');

        if ($this->accountSid && $this->authToken) {
            $this->client = new Client($this->accountSid, $this->authToken);
        }
    }

    /**
     * Enviar SMS de confirmación de cita
     */
    public function sendAppointmentConfirmation($appointment): bool
    {
        try {
            if (!$this->client) {
                // Si no está configurado, solo logueamos
                \Log::info('Twilio no configurado - Simulando envío de SMS', [
                    'phone' => $appointment->patient->user->phone,
                    'message' => $this->buildConfirmationMessage($appointment)
                ]);
                return true;
            }

            $phone = $this->formatPhone($appointment->patient->user->phone);
            $message = $this->buildConfirmationMessage($appointment);

            $this->client->messages->create(
                $phone,
                [
                    'from' => $this->twilioNumber,
                    'body' => $message
                ]
            );

            \Log::info('SMS de confirmación enviado', [
                'phone' => $phone,
                'appointment_id' => $appointment->id
            ]);

            return true;

        } catch (Exception $e) {
            \Log::error('Error enviando SMS de confirmación: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'phone' => $appointment->patient->user->phone
            ]);
            return false;
        }
    }

    /**
     * Enviar SMS de recordatorio de cita
     */
    public function sendAppointmentReminder($appointment): bool
    {
        try {
            if (!$this->client) {
                // Si no está configurado, solo logueamos
                \Log::info('Twilio no configurado - Simulando envío de SMS', [
                    'phone' => $appointment->patient->user->phone,
                    'message' => $this->buildReminderMessage($appointment)
                ]);
                return true;
            }

            $phone = $this->formatPhone($appointment->patient->user->phone);
            $message = $this->buildReminderMessage($appointment);

            $this->client->messages->create(
                $phone,
                [
                    'from' => $this->twilioNumber,
                    'body' => $message
                ]
            );

            \Log::info('SMS de recordatorio enviado', [
                'phone' => $phone,
                'appointment_id' => $appointment->id
            ]);

            return true;

        } catch (Exception $e) {
            \Log::error('Error enviando SMS de recordatorio: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'phone' => $appointment->patient->user->phone
            ]);
            return false;
        }
    }

    /**
     * Formatear número de teléfono para Twilio
     */
    private function formatPhone(string $phone): string
    {
        // Limpiar número (solo dígitos)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Asegurar formato internacional para México
        if (strlen($cleanPhone) === 10) {
            return '+52' . $cleanPhone;
        }
        
        // Si ya tiene código de país, asegurar que tenga +
        if (!str_starts_with($cleanPhone, '+')) {
            return '+' . $cleanPhone;
        }
        
        return $cleanPhone;
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
        
        return "🏥 CONFIRMACION DE CITA\n\n" .
               "✅ Cita Confirmada\n" .
               "📅 Fecha: {$formattedDate}\n" .
               "⏰ Hora: {$formattedTime}\n" .
               "👨‍⚕️ Doctor: Dr. {$doctor}\n" .
               "📍 Ubicacion: Clinica Medica\n\n" .
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
        
        return "⏰ RECORDATORIO DE CITA\n\n" .
               "📅 Manana tienes cita\n" .
               "🗓️ Fecha: {$formattedDate}\n" .
               "⏰ Hora: {$formattedTime}\n" .
               "👨‍⚕️ Doctor: Dr. {$doctor}\n" .
               "📍 Ubicacion: Clinica Medica\n\n" .
               "⚠️ Importante: Por favor arrive 10 minutos antes.\n\n" .
               "Si no puedes asistir, llama al 555-123-4567";
    }
}
