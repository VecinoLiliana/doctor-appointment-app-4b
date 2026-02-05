<x-admin-layout title="Pacientes | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Pacientes',
    ],
]">

    @livewire('admin.datatables.patient-table')

</x-admin-layout>
