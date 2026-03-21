<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        $specialities = Speciality::all();
        
        return view('admin.appointments.create', compact('patients', 'doctors', 'specialities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'nullable|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Establecer duration por defecto si no se proporciona
        if (!isset($data['duration'])) {
            $data['duration'] = 15;
        }

        // Establecer status por defecto
        $data['status'] = 1;

        $appointment = Appointment::create($data);

        // Enviar mensaje de confirmación por WhatsApp
        SendWhatsAppMessage::dispatch($appointment, 'confirmation');

        session()->flash('swall', [
            'icon' => 'success',
            'title' => 'Cita Creada',
            'text' => 'La cita fue creada exitosamente y se envió confirmación por WhatsApp',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        $specialities = Speciality::all();
        
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|integer|min:1|max:5',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Establecer duration por defecto si no se proporciona
        if (!isset($data['duration'])) {
            $data['duration'] = 15;
        }

        $appointment->update($data);

        session()->flash('swall', [
            'icon' => 'success',
            'title' => 'Cita Actualizada',
            'text' => 'La cita fue actualizada exitosamente',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        session()->flash('swall', [
            'icon' => 'success',
            'title' => 'Cita Eliminada',
            'text' => 'La cita fue eliminada exitosamente',
        ]);

        return redirect()->route('admin.appointments.index');
    }
}
