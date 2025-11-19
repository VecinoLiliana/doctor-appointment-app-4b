<?php

namespace App\Livewire\Admin\DataTables;


use Illuminate\Database\Query\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UserTable extends DataTableComponent
{
    //Se comenta para poder personalizar
    //protected $model = User::class;

    //Define el modelo y su consulta
    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return User::query()->with('roles');
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
            Column::make("Nombre", "name")
                ->sortable(),
            Column::make("Correo Electronico", "email")
                ->sortable(),
            Column::make("Numero de ID", "id_number")
                ->sortable(),
            Column::make("Telefono", "phone")
                ->sortable(),
            Column::make("Role", "roles")
                ->label(function ($row) {
                return $row->roles->first()?->name ?? 'Sin Rol';
                }),
            Column::make("Acciones", "phone")
                ->label(function ($row) {
                    return view('admin.users.actions', ['user' => $row]);
                })
        ];
    }
}
