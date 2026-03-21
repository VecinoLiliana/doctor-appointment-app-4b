<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Mostrar panel de control de WhatsApp
     */
    public function index()
    {
        $isConnected = $this->whatsappService->isConnected();
        $qrCode = null;

        if (!$isConnected) {
            $qrCode = $this->whatsappService->getQRCode();
        }

        return view('admin.whatsapp.index', compact('isConnected', 'qrCode'));
    }

    /**
     * Verificar estado de conexión
     */
    public function checkConnection()
    {
        try {
            $isConnected = $this->whatsappService->isConnected();
            
            return response()->json([
                'success' => true,
                'connected' => $isConnected
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking WhatsApp connection: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener QR code
     */
    public function getQRCode()
    {
        try {
            $qrCode = $this->whatsappService->getQRCode();
            
            return response()->json([
                'success' => true,
                'qr' => $qrCode
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting QR code: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar recordatorios manualmente
     */
    public function sendReminders()
    {
        try {
            $exitCode = Artisan::call('app:send-appointment-reminders');
            $output = Artisan::output();
            
            return response()->json([
                'success' => $exitCode === 0,
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending reminders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar mensaje de prueba
     */
    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            $success = $this->whatsappService->sendMessage(
                $request->phone,
                $request->message
            );
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Mensaje enviado correctamente' : 'Error al enviar mensaje'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending test message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
