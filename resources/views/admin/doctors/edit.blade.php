<x-admin-layout title="Doctores | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Doctores',
        'href' => route('admin.doctors.index'),
    ],
    [
        'name' => 'Editar',
    ],
]">

    <form action="{{route('admin.doctors.update', $doctor)}}" method="POST">
        @csrf
        @method('PUT')
        {{--Encabezado con foto y acciónes--}}
        <x-wire-card>
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <img src="{{$doctor->user->profile_photo_url}}" alt="{{$doctor->user->name}}"
                         class="h-20 w-20 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-2x1 font-bold text-gray-900">{{$doctor->user->name}}</p>
                        <p class="text-sm text-gray-500">Licencia: {{$doctor->medical_license_number ?: 'N/A'}}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index')}}">Volver</x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-2"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        <div class="mt-6">
            <x-wire-card>
                <div class="grid grid-cols-1 gap-6">
                    {{-- Especialidad --}}
                    <div>
                        <x-wire-native-select label="Especialidad" 
                            name="speciality_id"
                            id="speciality_id"
                        >
                            <option value="">Seleccione una especialidad</option>
                            @foreach($specialities as $speciality)
                                <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                                    {{ $speciality->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>
                    </div>

                    {{-- Licencia --}}
                    <div>
                        <x-wire-input label="Número de licencia médica" 
                            name="medical_license_number" 
                            id="medical_license_number"
                            value="{{old('medical_license_number', $doctor->medical_license_number)}}" 
                        />
                    </div>

                    {{-- Biografia --}}
                    <div>
                        <x-wire-textarea label="Biografía" 
                            name="biography" 
                            id="biography"
                            rows="4"
                        >{{old('biography', $doctor->biography)}}</x-wire-textarea>
                    </div>
                </div>
            </x-wire-card>
        </div>
    </form>

</x-admin-layout>