<x-admin-layout title="Editar Cita | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index')
    ],
    [
        'name' => 'Editar Cita',
    ],
]">
    <x-wire-card>
        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Primera fila: Paciente y Doctor -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <x-wire-native-select name="patient_id" label="Paciente" required>
                        <option value="">Seleccione un paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" 
                                    @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                {{ $patient->user->name }} - {{ $patient->user->id_number }}
                            </option>
                        @endforeach
                    </x-wire-native-select>

                    <x-wire-native-select name="doctor_id" label="Doctor" required>
                        <option value="">Seleccione un doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" 
                                    @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                                Dr. {{ $doctor->user->name }} - {{ $doctor->speciality->name ?? 'Sin especialidad' }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <!-- Segunda fila: Fecha, Hora y Especialidad -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <x-wire-input 
                        name="appointment_date" 
                        label="Fecha" 
                        type="date" 
                        required 
                        :min="now()->format('Y-m-d')"
                        :value="old('appointment_date', $appointment->appointment_date->format('Y-m-d'))"
                    />

                    <x-wire-input 
                        name="appointment_time" 
                        label="Hora" 
                        type="time" 
                        required 
                        :value="old('appointment_time', $appointment->appointment_time)"
                    />

                    <x-wire-native-select name="speciality_id" label="Especialidad (opcional)">
                        <option value="">Seleccione especialidad</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}" 
                                    @selected(old('speciality_id', $appointment->speciality_id) == $speciality->id)">
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <!-- Tercera fila: Estado y Motivo -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <x-wire-native-select name="status" label="Estado" required>
                        <option value="scheduled" @selected(old('status', $appointment->status) == 'scheduled')>Programada</option>
                        <option value="confirmed" @selected(old('status', $appointment->status) == 'confirmed')>Confirmada</option>
                        <option value="completed" @selected(old('status', $appointment->status) == 'completed')>Completada</option>
                        <option value="cancelled" @selected(old('status', $appointment->status) == 'cancelled')>Cancelada</option>
                        <option value="rescheduled" @selected(old('status', $appointment->status) == 'rescheduled')>Reprogramada</option>
                    </x-wire-native-select>

                    <div></div> <!-- Espacio vacío para alinear -->
                </div>

                <!-- Motivo de la cita -->
                <div>
                    <x-wire-textarea 
                        name="reason" 
                        label="Motivo de la cita" 
                        rows="3" 
                        placeholder="Describa el motivo de la consulta..."
                    >{{ old('reason', $appointment->reason) }}</x-wire-textarea>
                </div>

                <!-- Notas adicionales -->
                <div>
                    <x-wire-textarea 
                        name="notes" 
                        label="Notas adicionales" 
                        rows="3" 
                        placeholder="Notas sobre la cita..."
                    >{{ old('notes', $appointment->notes) }}</x-wire-textarea>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.appointments.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <i class="fa-solid fa-save mr-2"></i>
                    Actualizar Cita
                </button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
