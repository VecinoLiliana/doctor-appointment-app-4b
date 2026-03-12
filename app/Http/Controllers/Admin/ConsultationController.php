<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Mostrar formulario para atender una cita
     */
    public function attend(Appointment $appointment)
    {
        // Traer citas anteriores del mismo paciente que ya estén completadas (status = 3)
        // o que al menos tengan fecha anterior. Aquí usaremos status = 3 para garantizar que sean consultas terminadas.
        $previousConsultations = Appointment::with(['doctor.user'])
            ->where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', 3) // 3 = Completada
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('admin.consultations.attend', compact('appointment', 'previousConsultations'));
    }

    /**
     * Guardar consulta médica
     */
    public function store(Request $request, Appointment $appointment)
    {
        // Validación básica
        $data = $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:255',
            'medications.*.instructions' => 'nullable|string|max:500',
        ]);

        // Aquí guardaríamos la consulta en la base de datos
        // Por ahora solo actualizamos el estado de la cita a completada
        $appointment->update(['status' => 3]); // 3 = Completada

        session()->flash('swall', [
            'icon' => 'success',
            'title' => 'Consulta Guardada',
            'text' => 'La consulta fue registrada exitosamente',
        ]);

        return redirect()->route('admin.appointments.index');
    }
}
