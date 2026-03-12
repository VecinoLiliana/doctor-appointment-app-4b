<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        //
        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        //
        $specialities = Speciality::all();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'speciality_id' => 'required|exists:specialities,id',
            'medical_license_number' => 'required|string|max:20',
            'biography' => 'required|string',
        ]);

        $doctor->update($data);

        session()->flash('swall', [
            'icon' => 'success',
            'title' => '¡Éxito!',
            'text' => 'La información del doctor se actualizó correctamente.',
        ]);

        return redirect()->route('admin.doctors.edit', $doctor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }

    /**
     * Get doctor's schedule
     */
    public function schedule(Doctor $doctor)
    {
        $schedules = $doctor->schedules()->where('is_active', true)->orderBy('day')->get();
        
        return view('admin.doctors.schedule', compact('doctor', 'schedules'));
    }

    /**
     * Store bulk doctor schedules from grid UI
     */
    public function bulkUpdateSchedule(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'schedules' => 'array',
            'schedules.*.day' => 'required|string',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        // Delete existing active schedules for this doctor
        $doctor->schedules()->delete();

        if (!empty($data['schedules'])) {
            $schedulesToInsert = array_map(function ($schedule) use ($doctor) {
                return [
                    'doctor_id' => $doctor->id,
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['schedules']);

            Schedule::insert($schedulesToInsert);
        }

        return response()->json(['success' => true, 'message' => 'Horario guardado exitosamente.']);
    }

    /**
     * Store a new schedule
     */
    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean'
        ]);

        $data['doctor_id'] = $doctor->id;
        
        Schedule::create($data);

        return redirect()->route('admin.doctors.schedule', $doctor)
            ->with('success', 'Horario creado correctamente');
    }

    /**
     * Edit schedule
     */
    public function editSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        return response()->json($schedule);
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        $data = $request->validate([
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean'
        ]);

        $schedule->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->delete();

        return response()->json(['success' => true]);
    }
}


