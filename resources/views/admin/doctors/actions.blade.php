<div class="flex items-center space-x-2">
    <!-- Editar -->
    <x-wire-button href="{{route('admin.doctors.edit', $doctor )}}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    
    <!-- Ver horarios -->
    <x-wire-button href="{{route('admin.doctors.schedule', $doctor)}}" green xs>
        <i class="fa-solid fa-clock"></i>
    </x-wire-button>
</div>


