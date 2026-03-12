<div class="flex items-center space-x-2">
    <!-- Editar -->
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <!-- Consulta Medica Nueva -->
    @if($appointment->doctor)
        <x-wire-button href="{{ route('admin.consultations.attend', $appointment) }}" green xs>
            <i class="fa-solid fa-file-lines"></i>
        </x-wire-button>
    @endif
    
    <!-- Ver horarios del doctor (solo si hay doctor) -->
    @if($appointment->doctor)
        
        <!-- Atender cita original -->
        <x-wire-button href="{{ route('admin.consultations.attend', $appointment) }}" purple xs>
            <i class="fa-solid fa-stethoscope"></i>
        </x-wire-button>
    @endif
</div>


<!-- Modal para ver horarios del doctor (solo si hay doctor) -->
@if($appointment->doctor)
<div id="doctor_schedule_modal_{{ $appointment->doctor->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Horarios del Doctor</h2>
                <button onclick="closeDoctorSchedule({{ $appointment->doctor->id }})" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Contenido -->
        <div class="p-6">
            <!-- Información del doctor -->
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user-doctor text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Dr. {{ $appointment->doctor->user->name }}</h3>
                    <p class="text-gray-600">{{ $appointment->doctor->speciality->name ?? 'General' }}</p>
                    <p class="text-sm text-gray-500">Licencia: {{ $appointment->doctor->medical_license_number }}</p>
                </div>
            </div>

            <!-- Horarios disponibles -->
            <div class="space-y-4">
                <h4 class="text-lg font-medium text-gray-900">Horarios Disponibles</h4>
                
                <!-- Rangos de horarios -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">08:00 - 10:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">10:00 - 12:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">12:00 - 14:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">14:00 - 16:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">16:00 - 18:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span class="font-medium text-gray-900">18:00 - 20:00</span>
                        </div>
                        <p class="text-sm text-gray-600">4 intervalos disponibles</p>
                    </div>
                </div>

                <!-- Nota -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-sm text-blue-700">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        Cada intervalo es de 30 minutos. Los horarios mostrados son la disponibilidad general del doctor.
                    </p>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
            <button onclick="closeDoctorSchedule({{ $appointment->doctor->id }})" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
                Cerrar
            </button>
        </div>
    </div>
</div>
@endif

<script>
function showDoctorSchedule(doctorId) {
    document.getElementById('doctor_schedule_modal_' + doctorId).classList.remove('hidden');
}

function closeDoctorSchedule(doctorId) {
    document.getElementById('doctor_schedule_modal_' + doctorId).classList.add('hidden');
}
</script>
