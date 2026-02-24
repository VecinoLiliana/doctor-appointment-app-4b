<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Doctor;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class DoctorTable extends DataTableComponent
{

    public function builder(): Builder
    {
        return Doctor::query()->with(['user', 'speciality']);
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
            Column::make("Nombre", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Email", "user.email")
                ->sortable()
                ->searchable(),
            Column::make("Especialidad", "speciality.name")
                ->sortable()
                ->searchable(),
            Column::make("Licencia Médica", "medical_license_number")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return $value ?: 'N/A';
                }),
            Column::make("Telefono", "user.phone")
                ->sortable()
                ->searchable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.doctors.actions', ['doctor' => $row]);
                }),
        ];
    }
}


