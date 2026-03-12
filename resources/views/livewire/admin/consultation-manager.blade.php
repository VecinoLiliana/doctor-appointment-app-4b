<x-admin-layout title="Atender Cita | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index')
    ],
    [
        'name' => 'Atender Cita',
    ],
]">
    <div class="max-w-4xl mx-auto">
        <!-- Información de la cita -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Información de la Cita</h2>
                <button onclick="showPreviousConsultations()" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                    <i class="fa-solid fa-history mr-2"></i>
                    Consultas Anteriores
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Paciente</p>
                    <p class="font-medium">{{ $appointment->patient->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Doctor</p>
                    <p class="font-medium">Dr. {{ $appointment->doctor->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Fecha y Hora</p>
                    <p class="font-medium">{{ $appointment->date->format('d/m/Y') }} {{ $appointment->start_time->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs de Consulta -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button type="button" onclick="showTab('consulta')" id="tab-consulta" class="py-4 px-6 border-b-2 border-blue-500 font-medium text-blue-600">
                        <i class="fa-solid fa-stethoscope mr-2"></i>
                        Consulta
                    </button>
                    <button type="button" onclick="showTab('receta')" id="tab-receta" class="py-4 px-6 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-pills mr-2"></i>
                        Receta
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Tab Consulta -->
                <div id="content-consulta">
                    <form wire:submit="saveConsultation">
                        @csrf
                        <div class="space-y-6">
                            <!-- Diagnóstico -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Diagnóstico <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="diagnosis" 
                                          rows="4" 
                                          required
                                          placeholder="Describa el diagnóstico del paciente..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                @error('diagnosis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tratamiento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tratamiento <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="treatment" 
                                          rows="4" 
                                          required
                                          placeholder="Describa el tratamiento a seguir..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                @error('treatment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Notas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Notas Adicionales
                                </label>
                                <textarea wire:model="notes" 
                                          rows="3" 
                                          placeholder="Notas importantes sobre la consulta..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('admin.appointments.index') }}" 
                                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="fa-solid fa-save mr-2"></i>
                                    Guardar Consulta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab Receta -->
                <div id="content-receta" class="hidden">
                    <div class="space-y-6">
                        <!-- Agregar Medicamento -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar Medicamento</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" 
                                           wire:model="medicationName"
                                           placeholder="Ej: Paracetamol"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosis</label>
                                    <input type="text" 
                                           wire:model="medicationDosage"
                                           placeholder="Ej: 500mg"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones</label>
                                    <input type="text" 
                                           wire:model="medicationInstructions"
                                           placeholder="Ej: Cada 8 horas"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="addMedication"
                                    class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Agregar Medicamento
                            </button>
                        </div>

                        <!-- Lista de Medicamentos -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Medicamentos Recetados</h3>
                            @if(count($medications) > 0)
                                <div class="space-y-3">
                                    @foreach($medications as $index => $medication)
                                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $medication['name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $medication['dosage'] }} - {{ $medication['instructions'] }}</p>
                                            </div>
                                            <button type="button" 
                                                    wire:click="removeMedication({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fa-solid fa-pills text-4xl mb-3"></i>
                                    <p>No se han agregado medicamentos</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Consultas Anteriores -->
    <div id="previous-consultations-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4 max-h-[80vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Consultas Anteriores</h2>
                    <button onclick="closePreviousConsultations()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-history text-4xl mb-3"></i>
                    <p>No hay consultas anteriores registradas</p>
                    <p class="text-sm text-gray-400 mt-1">Esta función estará disponible próximamente</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button onclick="closePreviousConsultations()" 
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todo el contenido
            document.getElementById('content-consulta').classList.add('hidden');
            document.getElementById('content-receta').classList.add('hidden');
            
            // Quitar estilos activos de todos los tabs
            document.getElementById('tab-consulta').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('tab-consulta').classList.add('border-transparent', 'text-gray-500');
            document.getElementById('tab-receta').classList.remove('border-blue-500', 'text-blue-600');
            document.getElementById('tab-receta').classList.add('border-transparent', 'text-gray-500');
            
            // Mostrar el contenido y activar el tab seleccionado
            if (tabName === 'consulta') {
                document.getElementById('content-consulta').classList.remove('hidden');
                document.getElementById('tab-consulta').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-consulta').classList.add('border-blue-500', 'text-blue-600');
            } else if (tabName === 'receta') {
                document.getElementById('content-receta').classList.remove('hidden');
                document.getElementById('tab-receta').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('tab-receta').classList.add('border-blue-500', 'text-blue-600');
            }
        }

        function showPreviousConsultations() {
            document.getElementById('previous-consultations-modal').classList.remove('hidden');
        }

        function closePreviousConsultations() {
            document.getElementById('previous-consultations-modal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
