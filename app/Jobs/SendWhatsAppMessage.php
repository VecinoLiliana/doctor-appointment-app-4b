<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class SendWhatsAppMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Reintentos con espera incremental

    protected $appointment;
    protected $messageType;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment, string $messageType = 'confirmation')
    {
        $this->appointment = $appointment;
        $this->messageType = $messageType; // 'confirmation' o 'reminder'
    }

    /**
     * Execute the job.
     */
    public function handle(TwilioService $twilioService): void
    {
        try {
            $success = false;

            switch ($this->messageType) {
                case 'confirmation':
                    $success = $twilioService->sendAppointmentConfirmation($this->appointment);
                    break;
                case 'reminder':
                    $success = $twilioService->sendAppointmentReminder($this->appointment);
                    break;
                default:
                    throw new Exception("Invalid message type: {$this->messageType}");
            }

            if (!$success) {
                throw new Exception("Failed to send SMS message");
            }

            // Log exitoso
            \Log::info("SMS {$this->messageType} sent successfully", [
                'appointment_id' => $this->appointment->id,
                'patient' => $this->appointment->patient->user->name,
                'phone' => $this->appointment->patient->user->phone
            ]);

        } catch (Exception $e) {
            \Log::error("SMS message failed", [
                'appointment_id' => $this->appointment->id,
                'message_type' => $this->messageType,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Reintentar automáticamente si no es el último intento
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            }

            throw $e;
        }
    }

    /**
     * Job failed.
     */
    public function failed(Exception $exception): void
    {
        \Log::error("SMS job permanently failed", [
            'appointment_id' => $this->appointment->id,
            'message_type' => $this->messageType,
            'error' => $exception->getMessage()
        ]);
    }
}
