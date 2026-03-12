<x-admin-layout title="Consulta | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index')
    ],
    [
        'name' => 'Consulta',
    ],
]">
    <div class="w-full" x-data="consultationForm()">
        <!-- Header del Paciente -->
        <div class="p-6 flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $appointment->patient->user->name }}</h1>
                <p class="text-gray-500 text-sm">DNI: {{ $appointment->patient->user->dni ?? 'No registrado' }}</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" @click="showHistoryModal = true" class="px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-sm font-medium flex items-center">
                    <i class="fa-solid fa-file-medical text-gray-400 mr-2"></i> Ver Historia
                </button>
                <button type="button" @click="showPreviousModal = true" class="px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-sm font-medium flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-gray-400 mr-2"></i> Consultas Anteriores
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <!-- Tabs -->
            <div class="border-b border-gray-200 text-sm font-medium text-center text-gray-500 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button type="button" @click="activeTab = 'consulta'" 
                                :class="{'text-indigo-600 border-indigo-600': activeTab === 'consulta', 'border-transparent hover:text-gray-600 hover:border-gray-300 text-gray-500': activeTab !== 'consulta'}"
                                class="inline-flex py-4 px-4 font-semibold border-b-2 rounded-t-lg group items-center transition-colors">
                            <i class="fa-solid fa-stethoscope mr-2" :class="{'text-indigo-600': activeTab === 'consulta', 'text-gray-400 group-hover:text-gray-500': activeTab !== 'consulta'}"></i> Consulta
                        </button>
                    </li>
                    <li class="mr-2">
                        <button type="button" @click="activeTab = 'receta'"
                                :class="{'text-blue-600 border-blue-600': activeTab === 'receta', 'border-transparent hover:text-gray-600 hover:border-gray-300 text-gray-500': activeTab !== 'receta'}"
                                class="inline-flex py-4 px-4 font-semibold border-b-2 rounded-t-lg group items-center transition-colors">
                            <i class="fa-solid fa-prescription-bottle-medical mr-2" :class="{'text-blue-600': activeTab === 'receta', 'text-gray-400 group-hover:text-gray-500': activeTab !== 'receta'}"></i> Receta
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Form parameters -->
            <form action="{{ route('admin.consultations.store', $appointment) }}" method="POST">
                @csrf
                
                <!-- Pestaña Consulta -->
                <div x-show="activeTab === 'consulta'" class="space-y-6">
                    <!-- Diagnóstico -->
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico</label>
                        <textarea id="diagnosis" name="diagnosis" rows="4" required
                            class="w-full rounded-md border-indigo-300 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm sm:text-sm resize-y"></textarea>
                    </div>

                    <!-- Tratamiento -->
                    <div>
                        <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">Tratamiento</label>
                        <textarea id="treatment" name="treatment" rows="4" required
                            class="w-full rounded-md border-gray-300 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm sm:text-sm resize-y"></textarea>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                        <textarea id="notes" name="notes" rows="4"
                            class="w-full rounded-md border-gray-300 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm sm:text-sm resize-y"></textarea>
                    </div>
                </div>

                <!-- Pestaña Receta -->
                <div x-show="activeTab === 'receta'" style="display: none;" class="space-y-4">
                    <template x-for="(med, index) in medications" :key="index">
                        <div class="flex items-end space-x-4 w-full">
                            <div class="flex-grow">
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-show="index === 0">Medicamento</label>
                                <input type="text" x-model="med.name" :name="`medications[${index}][name]`" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div class="w-1/4">
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-show="index === 0">Dosis</label>
                                <input type="text" x-model="med.dosage" :name="`medications[${index}][dosage]`"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div class="flex-grow">
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-show="index === 0">Frecuencia / Duración</label>
                                <input type="text" x-model="med.instructions" :name="`medications[${index}][instructions]`"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <button type="button" @click="removeMedication(index)" class="mb-1 p-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors shadow-sm flex items-center justify-center w-10 h-10">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <div class="pt-2">
                        <button type="button" @click="addMedication()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 flex items-center text-sm font-medium shadow-sm transition-colors">
                            <i class="fa-solid fa-plus mr-2"></i> Añadir Medicamento
                        </button>
                    </div>
                </div>

                <!-- Footer button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" 
                            :class="{'bg-indigo-500 hover:bg-indigo-600 focus:ring-indigo-500': activeTab === 'consulta', 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500': activeTab === 'receta'}"
                            class="inline-flex justify-center px-6 py-2.5 text-sm font-semibold text-white border border-transparent rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-2 flex items-center"></i> Guardar Consulta
                    </button>
                </div>
            </form>
        </div>

        <!-- History Modal -->
        <div x-show="showHistoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Background overlay -->
                <div x-show="showHistoryModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showHistoryModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <!-- Modal panel -->
                <div x-show="showHistoryModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                            Historia médica del paciente
                        </h3>
                        <button type="button" @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Tipo de sangre:</p>
                            <p class="text-base font-semibold text-gray-900">{{ $appointment->patient->bloodType->name ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Alergias:</p>
                            <p class="text-base font-semibold text-gray-900">{{ $appointment->patient->allergies ?? 'No registradas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Enfermedades crónicas:</p>
                            <p class="text-base font-semibold text-gray-900">{{ $appointment->patient->chronic_conditions ?? 'No registradas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Antecedentes quirúrgicos:</p>
                            <p class="text-base font-semibold text-gray-900">{{ $appointment->patient->surgical_history ?? 'No registrados' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.patients.edit', $appointment->patient->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm transition-colors cursor-pointer">
                            Ver / Editar Historia Médica
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Consultations Modal -->
        <div x-show="showPreviousModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Background overlay -->
                <div x-show="showPreviousModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showPreviousModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <!-- Modal panel -->
                <div x-show="showPreviousModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-lg pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full flex-col max-h-[85vh] flex">
                    
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 px-8 shrink-0">
                        <h3 class="text-xl leading-6 font-semibold text-gray-700" id="modal-title">
                            Consultas Anteriores
                        </h3>
                        <button type="button" @click="showPreviousModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Cerrar</span>
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-6 space-y-5 flex-1 custom-scrollbar">
                        @forelse($previousConsultations ?? [] as $prevAppointment)
                        <div class="border border-indigo-200 rounded-xl p-6 bg-white shadow-sm transition-all hover:shadow-md">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="flex items-center text-indigo-700 font-bold mb-1 text-base">
                                        <i class="fa-solid fa-calendar-days mr-2"></i>
                                        {{ \Carbon\Carbon::parse($prevAppointment->date)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($prevAppointment->start_time)->format('H:i') }}
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">Atendido por: Dr(a). {{ $prevAppointment->doctor->user->name ?? 'Desconocido' }}</p>
                                </div>
                                <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2 border border-indigo-400 text-indigo-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-sm font-semibold transition-colors text-center focus:ring-2 focus:ring-indigo-300">
                                    Consultar Detalle
                                </a>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-5 text-sm space-y-3 mt-4 text-gray-600 flex flex-col justify-center">
                                <p><strong class="text-gray-800">Diagnóstico:</strong> {{ $prevAppointment->diagnosis ?? 'No registrado' }}</p>
                                <p><strong class="text-gray-800">Tratamiento:</strong> {{ $prevAppointment->treatment ?? 'No registrado' }}</p>
                                @if($prevAppointment->notes)
                                <p><strong class="text-gray-800">Notas:</strong> {{ $prevAppointment->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-16 px-4">
                            <i class="fa-solid fa-folder-open text-6xl mb-4 text-gray-200"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Sin Consultas Anteriores</h3>
                            <p class="text-gray-500">Este paciente aún no tiene un historial de consultas completadas en el sistema.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('consultationForm', () => ({
                showHistoryModal: false,
                showPreviousModal: false,
                activeTab: 'consulta',
                medications: [
                    { name: '', dosage: '', instructions: '' }
                ],
                addMedication() {
                    this.medications.push({ name: '', dosage: '', instructions: '' });
                },
                removeMedication(index) {
                    if (this.medications.length > 1) {
                        this.medications.splice(index, 1);
                    } else {
                        // Limpiamos los campos si es el único item
                        this.medications[0] = { name: '', dosage: '', instructions: '' };
                    }
                }
            }))
        })
    </script>
</x-admin-layout>
