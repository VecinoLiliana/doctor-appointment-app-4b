<x-admin-layout title="Detalle de Cita | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Citas',
        'href' => route('admin.appointments.index'),
    ],
    [
        'name' => 'Detalle',
    ],
]">
    <x-wire-card>
        <div class="space-y-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Información de la Cita</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.appointments.edit', $appointment) }}" 
                       class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        <i class="fa-solid fa-edit mr-1"></i> Editar
                    </a>
                    <a href="{{ route('admin.appointments.index') }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Volver
                    </a>
                </div>
            </div>

            <!-- Información de la cita -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Detalles de la Cita</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Fecha:</dt>
                            <dd class="font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Hora:</dt>
                            <dd class="font-medium">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Estado:</dt>
                            <dd>
                                @switch($appointment->status)
                                    @case('scheduled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Programada</span>
                                        @break
                                    @case('confirmed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Confirmada</span>
                                        @break
                                    @case('completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completada</span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelada</span>
                                        @break
                                    @case('rescheduled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Reprogramada</span>
                                        @break
                                @endswitch
                            </dd>
                        </div>
                        @if($appointment->speciality)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Especialidad:</dt>
                            <dd class="font-medium">{{ $appointment->speciality->name }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Participantes</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-gray-600">Paciente:</dt>
                            <dd class="font-medium">{{ $appointment->patient->user->name }}</dd>
                            <dd class="text-sm text-gray-500">{{ $appointment->patient->user->email }}</dd>
                        </div>
                        <div class="pt-2">
                            <dt class="text-gray-600">Doctor:</dt>
                            <dd class="font-medium">Dr. {{ $appointment->doctor->user->name }}</dd>
                            <dd class="text-sm text-gray-500">{{ $appointment->doctor->user->email }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Motivo y notas -->
            @if($appointment->reason || $appointment->notes)
            <div class="border-t pt-6">
                <h4 class="font-medium text-gray-900 mb-4">Información Adicional</h4>
                @if($appointment->reason)
                <div class="mb-4">
                    <dt class="text-gray-600">Motivo de la cita:</dt>
                    <dd class="mt-1 text-gray-900">{{ $appointment->reason }}</dd>
                </div>
                @endif
                @if($appointment->notes)
                <div>
                    <dt class="text-gray-600">Notas:</dt>
                    <dd class="mt-1 text-gray-900">{{ $appointment->notes }}</dd>
                </div>
                @endif
            </div>
            @endif
        </div>
    </x-wire-card>
</x-admin-layout>
