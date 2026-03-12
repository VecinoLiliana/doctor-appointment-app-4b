<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()
            ->select('appointments.*')
            ->with(['patient.user', 'doctor.user', 'speciality']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Fecha", "date")
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),
            Column::make("Hora", "start_time")
                ->sortable()
                ->format(fn($value) => date('h:i A', strtotime($value))),
            Column::make("Paciente", "patient.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Doctor", "doctor.user.name")
                ->sortable()
                ->searchable(),
            Column::make("Especialidad", "speciality.name")
                ->sortable()
                ->searchable(),
            Column::make("Estado", "status")
                ->sortable()
                ->format(fn($value) => match($value) {
                    1 => 'Programada',
                    2 => 'Confirmada',
                    3 => 'Completada',
                    4 => 'Cancelada',
                    5 => 'Reprogramada',
                    default => 'Programada'
                }),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.appointments.actions', ['appointment' => $row]);
                }),
        ];
    }
}
