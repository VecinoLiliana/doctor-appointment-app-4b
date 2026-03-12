<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Buscar doctores disponibles para una fecha y rango de tiempo específicos
     */
    public function search(Request $request)
    {
        $request->validate([
            'search_date' => 'required|date',
            'search_time' => 'required', // Ahora es un rango, ej: "08:00-10:00"
            'search_speciality' => 'nullable|exists:specialities,id',
        ]);

        $date = $request->search_date;
        $timeRange = $request->search_time;
        $specialityId = $request->search_speciality;

        // Obtener doctores con sus especialidades
        $query = Doctor::with(['user', 'speciality']);

        // Filtrar por especialidad si se especifica
        if ($specialityId) {
            $query->where('speciality_id', $specialityId);
        }

        $doctors = $query->get();

        // Filtrar doctores disponibles para el rango de tiempo
        $availableDoctors = $doctors->filter(function ($doctor) use ($date, $timeRange) {
            return $this->isDoctorAvailableInRange($doctor, $date, $timeRange);
        })->values();

        return response()->json([
            'available_doctors' => $availableDoctors,
            'search_date' => $date,
            'search_time_range' => $timeRange,
            'speciality_id' => $specialityId,
        ]);
    }

    /**
     * Verificar si un doctor está disponible en un rango de tiempo específico
     */
    private function isDoctorAvailableInRange($doctor, $date, $timeRange)
    {
        // Parsear el rango de tiempo
        $times = explode('-', $timeRange);
        if (count($times) != 2) {
            return false;
        }

        $startTime = $times[0];
        $endTime = $times[1];

        // Generar todos los intervalos de 30 minutos en el rango
        $timeSlots = $this->generateTimeSlots($startTime, $endTime);

        // Verificar si el doctor tiene citas en alguno de los intervalos
        $existingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('date', $date)
            ->whereIn('start_time', $timeSlots)
            ->where('status', '!=', 4) // 4 = Cancelada (tinyInteger)
            ->pluck('start_time')
            ->toArray();

        // Filtrar los horarios disponibles
        $availableSlots = array_diff($timeSlots, $existingAppointments);

        // Si hay al menos un horario disponible, el doctor está disponible
        return !empty($availableSlots);
    }

    /**
     * Generar intervalos de 30 minutos dentro de un rango
     */
    private function generateTimeSlots($startTime, $endTime)
    {
        $slots = [];
        [$startHour, $startMinute] = explode(':', $startTime);
        [$endHour, $endMinute] = explode(':', $endTime);

        $currentHour = (int)$startHour;
        $currentMinute = (int)$startMinute;
        $endHourTotal = (int)$endHour;
        $endMinuteTotal = (int)$endMinute;

        while ($currentHour < $endHourTotal || ($currentHour == $endHourTotal && $currentMinute < $endMinuteTotal)) {
            $slots[] = sprintf('%02d:%02d', $currentHour, $currentMinute);
            
            $currentMinute += 30;
            if ($currentMinute >= 60) {
                $currentMinute = 0;
                $currentHour++;
            }
        }

        return $slots;
    }
}
