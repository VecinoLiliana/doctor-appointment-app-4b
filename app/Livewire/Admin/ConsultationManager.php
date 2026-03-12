<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Appointment;

class ConsultationManager extends Component
{
    public $appointment;
    public $diagnosis = '';
    public $treatment = '';
    public $notes = '';
    public $medications = [];
    public $medicationName = '';
    public $medicationDosage = '';
    public $medicationInstructions = '';

    protected $rules = [
        'diagnosis' => 'required|string|max:1000',
        'treatment' => 'required|string|max:1000',
        'notes' => 'nullable|string|max:2000',
    ];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function addMedication()
    {
        if ($this->medicationName && $this->medicationDosage) {
            $this->medications[] = [
                'name' => $this->medicationName,
                'dosage' => $this->medicationDosage,
                'instructions' => $this->medicationInstructions
            ];
            
            $this->reset(['medicationName', 'medicationDosage', 'medicationInstructions']);
        }
    }

    public function removeMedication($index)
    {
        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
    }

    public function saveConsultation()
    {
        $this->validate();
        
        // Aquí guardaríamos la consulta en la base de datos
        // Por ahora solo mostramos un mensaje de éxito
        
        session()->flash('message', 'Consulta guardada exitosamente');
        
        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager');
    }
}
