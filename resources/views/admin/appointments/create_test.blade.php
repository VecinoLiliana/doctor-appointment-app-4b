<x-admin-layout title="Nueva Cita | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index')
    ],
    [
        'name' => 'Nuevo',
    ],
]">
    <div class="w-full">
        <!-- Debug: Mostrar datos -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Debug Information</h3>
            <p>Pacientes: {{ $patients->count() }}</p>
            <p>Doctores: {{ $doctors->count() }}</p>
            <p>Especialidades: {{ $specialities->count() }}</p>
        </div>

        <!-- Sección: Buscar disponibilidad -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Buscar disponibilidad</h2>
            </div>
            
            <p class="text-gray-600 mb-6">
                Encuentra el horario perfecto para tu cita.
            </p>

            <div class="flex flex-wrap items-end gap-4 mb-6">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha
                    </label>
                    <input type="date" 
                           id="search_date"
                           name="search_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hora
                    </label>
                    <select id="search_time_range" 
                            name="search_time_range" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Selecciona un rango</option>
                        <option value="08:00-10:00">08:00 - 10:00</option>
                        <option value="10:00-12:00">10:00 - 12:00</option>
                        <option value="12:00-14:00">12:00 - 14:00</option>
                        <option value="14:00-16:00">14:00 - 16:00</option>
                        <option value="16:00-18:00">16:00 - 18:00</option>
                        <option value="18:00-20:00">18:00 - 20:00</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Especialidad (opcional)
                    </label>
                    <select id="search_speciality"
                            name="search_speciality" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Selecciona una especialidad</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" 
                        id="search_availability"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Buscar disponibilidad
                </button>
            </div>

            <!-- Resultados de búsqueda -->
            <div id="search_results" class="hidden">
                <div class="border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Doctores Disponibles</h3>
                    <div id="doctors_list" class="space-y-3">
                        <!-- Los resultados se cargarán aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
