<x-admin-layout title="Usuarios | MediLink" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Usuarios']
]">
    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.users.create') }}">
           <i class="fa-solid fa-plus w-4 h-4"></i>
            <span class="ml-1">Nuevo</span>
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.user-table')


</x-admin-layout>
