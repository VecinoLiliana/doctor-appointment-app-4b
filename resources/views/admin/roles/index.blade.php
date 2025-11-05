<x-admin-layout title="Roles | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Roles',
    ],
]">

    <x-slot name="action">
        <x-wire-button blue href="{{route('admin.roles.create')}}">
            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nuevo</span>
            </span>
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.role-table')
</x-admin-layout>
