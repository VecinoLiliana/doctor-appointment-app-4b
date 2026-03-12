<x-admin-layout title="Doctores | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Doctores',
    ],
]">

    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.doctors.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
            <i class="fa-solid fa-plus mr-2"></i>
            Nuevo
        </a>
    </div>

    @livewire('admin.datatables.doctor-table')

</x-admin-layout>
