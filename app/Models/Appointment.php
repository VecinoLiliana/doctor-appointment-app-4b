<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'speciality_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    // Relación inversa a través de doctor
    public function user()
    {
        return $this->hasOneThrough(User::class, Doctor::class, 'id', 'id', 'doctor_id', 'user_id');
    }
}
