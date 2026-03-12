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
        <!-- Sección: Buscar disponibilidad -->
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center mb-1">
                <h2 class="text-xl font-bold text-gray-900">Buscar disponibilidad</h2>
            </div>
            
            <p class="text-gray-500 text-sm mb-6">
                Encuentra el horario perfecto para tu cita.
            </p>

            <div class="flex flex-wrap items-end gap-4 mb-6">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-500 mb-1">
                        Fecha
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="search_date"
                               name="search_date" 
                               class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-500 mb-1">
                        Hora
                    </label>
                    <select id="search_time_range" 
                            name="search_time_range" 
                            class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors appearance-none">
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
                    <label class="block text-sm font-medium text-gray-500 mb-1">
                        Especialidad (opcional)
                    </label>
                    <select id="search_speciality"
                            name="search_speciality" 
                            class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors appearance-none">
                        <option value="">Endocrinología (Seleccione otra para cambiar)</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" 
                        id="search_availability"
                        class="px-8 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200 font-medium whitespace-nowrap lg:ml-4">
                    Buscar disponibilidad
                </button>
            </div>

            <!-- Resultados de búsqueda -->
            <div id="search_results" class="hidden">
                <div class="border-t border-gray-100 pt-6 mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Lista de doctores -->
                    <div class="mb-8 lg:col-span-2">
                        <div id="doctors_list" class="space-y-4">
                            <!-- Los resultados se cargarán aquí -->
                        </div>
                    </div>

                    <!-- Resumen de la cita (siempre visible) -->
                    <div class="lg:sticky lg:top-6 lg:col-span-1">
                        <div id="appointment_summary_container" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Resumen de la cita</h3>
                                <!-- Formulario del resumen -->
                                <form id="appointment_summary_form" action="{{ route('admin.appointments.store') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Campos ocultos para el envío -->
                                    <input type="hidden" name="doctor_id" id="summary_doctor_id">
                                    <input type="hidden" name="date" id="summary_date">
                                    <input type="hidden" name="start_time" id="summary_start_time">
                                    <input type="hidden" name="end_time" id="summary_end_time">
                                    <input type="hidden" name="duration" value="15">

                                    <!-- Datos de lectura -->
                                    <div class="space-y-4 mb-8 text-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Doctor:</span>
                                            <span class="font-medium text-gray-900" id="summary_doctor_display">Dr. --</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Fecha:</span>
                                            <span class="font-medium text-gray-900" id="summary_date_display">--</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Horario:</span>
                                            <span class="font-medium text-gray-900" id="summary_time_display">--</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Duración:</span>
                                            <span class="font-medium text-gray-900">15 minutos</span>
                                        </div>
                                    </div>

                                    <!-- Paciente -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-500 mb-2">
                                            Paciente
                                        </label>
                                        <select name="patient_id" required class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 appearance-none">
                                            <option value="">Seleccione un paciente</option>
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->id }}">
                                                    {{ $patient->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Motivo -->
                                    <div class="mb-8">
                                        <label class="block text-sm font-medium text-gray-500 mb-2">
                                            Motivo de la cita
                                        </label>
                                        <textarea name="reason" 
                                                  rows="4" 
                                                  required
                                                  class="w-full px-4 py-3 text-gray-700 border-2 border-indigo-500 rounded-lg focus:ring-0 focus:border-indigo-500 resize-none"></textarea>
                                    </div>

                                    <!-- Botones -->
                                    <button type="submit" id="confirm_appointment_btn" disabled class="w-full py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors font-medium disabled:bg-indigo-300 disabled:cursor-not-allowed">
                                        Confirmar cita
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje de no resultados -->
            <div id="no_results" class="hidden">
                <div class="border-t pt-6 mt-6">
                    <div class="text-center py-8">
                        <i class="fa-solid fa-user-doctor-slash text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">No hay doctores disponibles en este horario.</p>
                        <p class="text-sm text-gray-500 mt-1">Intenta con otra fecha, hora o especialidad.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de la cita (aparece al seleccionar horario) -->
        <div id="appointment_summary" class="hidden mt-6">
            <form action="{{ route('admin.appointments.store') }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Resumen de la cita</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Doctor seleccionado (solo lectura) -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Doctor seleccionado
                                </label>
                                <input type="text" 
                                       id="selected_doctor_display"
                                       readonly
                                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900">
                                <input type="hidden" name="doctor_id" id="selected_doctor_id">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Paciente
                                </label>
                                <select name="patient_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Seleccione un paciente</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">
                                            {{ $patient->user->name }} - {{ $patient->user->id_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Fecha y hora (solo lectura) -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de la cita
                                </label>
                                <input type="date" 
                                       name="date" 
                                       id="appointment_date"
                                       required
                                       readonly
                                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de inicio
                                </label>
                                <input type="time" 
                                       name="start_time" 
                                       id="appointment_time"
                                       required
                                       readonly
                                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de fin
                                </label>
                                <input type="time" 
                                       name="end_time" 
                                       id="appointment_end_time"
                                       required
                                       readonly
                                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900">
                            </div>
                        </div>

                        <!-- Especialidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Especialidad (opcional)
                            </label>
                            <select name="speciality_id" 
                                    id="appointment_speciality"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Seleccione especialidad</option>
                                @foreach($specialities as $speciality)
                                    <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Motivo de la cita -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la cita
                            </label>
                            <textarea name="reason" 
                                      rows="4" 
                                      placeholder="Describa el motivo de la consulta..."
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        </div>

                        <!-- Notas adicionales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notas adicionales
                            </label>
                            <textarea name="notes" 
                                      rows="3" 
                                      placeholder="Notas importantes sobre la cita..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" 
                                onclick="resetSearch()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Volver a búsqueda
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fa-solid fa-save mr-2"></i>
                            Guardar Cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para seleccionar doctor y horario -->
    <div id="doctor_selection_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Seleccionar Doctor y Horario</h2>
                    <button onclick="closeDoctorSelectionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Lista de doctores -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Doctores Disponibles</h3>
                        <div id="modal_doctors_list" class="space-y-3 max-h-96 overflow-y-auto">
                            <!-- Los resultados se cargarán aquí -->
                        </div>
                    </div>

                    <!-- Información del doctor seleccionado -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Doctor</h3>
                        <div id="modal_doctor_info" class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="text-center text-gray-500">
                                <i class="fa-solid fa-user-doctor text-4xl mb-3"></i>
                                <p class="text-sm">Seleccione un doctor para ver su información</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button onclick="closeDoctorSelectionModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        let searchResults = [];

        // Event listener para el botón de buscar disponibilidad
        document.getElementById('search_availability').addEventListener('click', function() {
            const date = document.getElementById('search_date').value;
            const timeRange = document.getElementById('search_time_range').value;
            const speciality = document.getElementById('search_speciality').value;

            if (!date || !timeRange) {
                alert('Por favor selecciona fecha y rango de horarios');
                return;
            }

            // Mostrar loading
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Buscando...';
            this.disabled = true;

            // Buscar disponibilidad
            fetchAvailability(date, timeRange, speciality);

            // Restaurar botón
            setTimeout(() => {
                this.innerHTML = 'Buscar disponibilidad';
                this.disabled = false;
            }, 1000);
        });

        function fetchAvailability(date, timeRange, speciality) {
            console.log('Buscando disponibilidad:', { date, timeRange, speciality });
            
            fetch('/admin/availability/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    search_date: date,
                    search_time: timeRange,
                    search_speciality: speciality
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                searchResults = data;
                
                try {
                    if (data.available_doctors && data.available_doctors.length > 0) {
                        console.log('Processing doctors:', data.available_doctors.length, 'doctors found');
                        
                        // Mostrar resultados
                        document.getElementById('search_results').classList.remove('hidden');
                        document.getElementById('no_results').classList.add('hidden');
                        
                        // Cargar doctores
                        const doctorsList = document.getElementById('doctors_list');
                        console.log('Doctors list element:', doctorsList);
                        
                        const doctorsHTML = data.available_doctors.map(doctor => {
                            console.log('Processing doctor:', doctor);
                            
                            const nameParts = doctor.user.name.split(' ');
                            const initials = nameParts.length > 1 ? nameParts[0][0] + nameParts[1][0] : nameParts[0].substring(0, 2).toUpperCase();

                            return `
                            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] p-6 mb-6">
                                <div class="flex items-center space-x-4 mb-6">
                                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-500 font-bold text-xl">${initials}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">Dr. ${doctor.user.name}</h4>
                                        <p class="text-sm text-indigo-500">${doctor.speciality?.name || 'Endocrinología'}</p>
                                    </div>
                                </div>
                                <div class="border-t border-gray-100 pt-5">
                                    <p class="text-sm font-bold text-gray-500 mb-3">Horarios disponibles:</p>
                                    <div class="flex flex-wrap gap-3">
                                        ${(() => {
                                            console.log('About to generate time slots with:', {
                                                date: data.search_date,
                                                timeRange: data.search_time_range,
                                                doctorId: doctor.id
                                            });
                                            return generateTimeSlots(data.search_date, data.search_time_range, doctor.id);
                                        })()}
                                    </div>
                                </div>
                            </div>
                        `;
                        }).join('');
                        
                        console.log('Generated HTML length:', doctorsHTML.length);
                        doctorsList.innerHTML = doctorsHTML;
                        console.log('HTML inserted successfully');
                    } else {
                        console.log('No doctors available');
                        // Mostrar mensaje de no resultados
                        document.getElementById('search_results').classList.remove('hidden');
                        document.getElementById('no_results').classList.remove('hidden');
                        document.getElementById('doctors_list').innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error processing doctors:', error);
                    alert('Error procesando los resultados: ' + error.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al buscar disponibilidad');
            });
        }

        function showDoctorInfo(doctor) {
            const infoDiv = document.getElementById('modal_doctor_info');
            
            infoDiv.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user-doctor text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Dr. ${doctor.user.name}</h4>
                            <p class="text-sm text-gray-600">${doctor.speciality?.name || 'General'}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Licencia:</span> ${doctor.medical_license_number}
                        </p>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Email:</span> ${doctor.user.email}
                        </p>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Teléfono:</span> ${doctor.user.phone}
                        </p>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Horarios disponibles:</p>
                        <div class="flex flex-wrap gap-2">
                            ${generateTimeSlots(searchResults.search_date, searchResults.search_time_range, doctor.id)}
                        </div>
                    </div>
                </div>
            `;
        }

        function generateTimeSlots(date, timeRange, doctorId) {
            try {
                console.log('Generating time slots for:', { date, timeRange, doctorId });
                console.log('TimeRange type:', typeof timeRange);
                console.log('TimeRange value:', JSON.stringify(timeRange));
                
                if (!timeRange || typeof timeRange !== 'string') {
                    console.log('Invalid time range - not a string:', timeRange);
                    return '<p class="text-red-500">Rango de tiempo inválido</p>';
                }
                
                const [startTime, endTime] = timeRange.split('-');
                console.log('Split times:', { startTime, endTime });
                
                if (!startTime || !endTime) {
                    console.log('Invalid time range - missing parts:', timeRange);
                    return '<p class="text-red-500">Formato de rango inválido</p>';
                }
                
                const [startHour, startMinute] = startTime.split(':');
                const [endHour, endMinute] = endTime.split(':');
                console.log('Parsed times:', { startHour, startMinute, endHour, endMinute });
                
                let currentHour = parseInt(startHour);
                let currentMinute = parseInt(startMinute);
                const endHourTotal = parseInt(endHour);
                const endMinuteTotal = parseInt(endMinute);
                
                if (isNaN(currentHour) || isNaN(currentMinute) || isNaN(endHourTotal) || isNaN(endMinuteTotal)) {
                    console.log('Invalid time values:', { currentHour, currentMinute, endHourTotal, endMinuteTotal });
                    return '<p class="text-red-500">Valores de tiempo inválidos</p>';
                }
                
                const slots = [];
                
                while (currentHour < endHourTotal || (currentHour == endHourTotal && currentMinute < endMinuteTotal)) {
                    const timeString = `${currentHour.toString().padStart(2, '0')}:${currentMinute.toString().padStart(2, '0')}`;
                    slots.push(`<button type="button" onclick="selectTimeSlot(${doctorId}, '${date}', '${timeString}', this)" class="px-6 py-2 bg-indigo-200/50 text-indigo-500 rounded-md hover:bg-indigo-500 hover:text-white transition-colors duration-200 font-medium time-slot-btn">${timeString}</button>`);
                    
                    currentMinute += 15;
                    if (currentMinute >= 60) {
                        currentMinute = 0;
                        currentHour++;
                    }
                }
                
                console.log('Generated slots:', slots.length, 'slots');
                console.log('Slots array:', slots);
                return slots.join('');
            } catch (error) {
                console.error('Error in generateTimeSlots:', error);
                console.error('Error stack:', error.stack);
                return '<p class="text-red-500">Error generando horarios</p>';
            }
        }

        function selectTimeSlot(doctorId, date, time, btnElement) {
            // Remover estado activo de todos los botones
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-500', 'text-white');
                btn.classList.add('bg-indigo-200/50', 'text-indigo-500');
            });
            
            // Agregar estado activo al botón seleccionado
            btnElement.classList.remove('bg-indigo-200/50', 'text-indigo-500');
            btnElement.classList.add('bg-indigo-500', 'text-white');

            // Encontrar el doctor seleccionado
            const doctor = searchResults.available_doctors.find(d => d.id == doctorId);
            
            // Calcular hora de fin (15 minutos después)
            const [hours, minutes] = time.split(':');
            const endTime = new Date();
            endTime.setHours(parseInt(hours), parseInt(minutes) + 15, 0);
            const formattedEndTime = endTime.toTimeString().slice(0, 5);
            
            // Actualizar el resumen de la cita
            updateAppointmentSummary(doctor, date, time, formattedEndTime);
        }

        function updateAppointmentSummary(doctor, date, time, endTime) {
            // Formatear fecha para mostrar
            const dateObj = new Date(date + 'T00:00:00');
            const formattedDate = dateObj.toISOString().split('T')[0];
            
            // Formatear horario
            const timeRange = `${time}:00 - ${endTime}:00`;

            // Llenar datos visibles
            document.getElementById('summary_doctor_display').textContent = `Dr. ${doctor.user.name}`;
            document.getElementById('summary_date_display').textContent = formattedDate;
            document.getElementById('summary_time_display').textContent = timeRange;
            
            // Llenar campos ocultos del formulario
            document.getElementById('summary_doctor_id').value = doctor.id;
            document.getElementById('summary_date').value = date;
            document.getElementById('summary_start_time').value = time;
            document.getElementById('summary_end_time').value = endTime;
            
            // Habilitar botón de confirmar
            const confirmBtn = document.getElementById('confirm_appointment_btn');
            confirmBtn.disabled = false;
        }

        function openDoctorSelectionModal() {
            const date = document.getElementById('search_date').value;
            const timeRange = document.getElementById('search_time_range').value;
            const speciality = document.getElementById('search_speciality').value;

            if (!date || !timeRange) {
                alert('Por favor selecciona fecha y rango de horarios primero');
                return;
            }

            // Mostrar modal
            document.getElementById('doctor_selection_modal').classList.remove('hidden');
            
            // Realizar búsqueda
            fetchAvailability(date, timeRange, speciality);
        }

        function closeDoctorSelectionModal() {
            document.getElementById('doctor_selection_modal').classList.add('hidden');
        }

        function showDoctorInfo(doctor) {
            const infoDiv = document.getElementById('modal_doctor_info');
            
            infoDiv.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user-doctor text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Dr. ${doctor.user.name}</h4>
                            <p class="text-sm text-gray-600">${doctor.speciality?.name || 'General'}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Licencia:</span> ${doctor.medical_license_number}
                        </p>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Email:</span> ${doctor.user.email}
                        </p>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Teléfono:</span> ${doctor.user.phone}
                        </p>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Horarios disponibles:</p>
                        <div class="flex flex-wrap gap-2">
                            ${generateTimeSlots(searchResults.search_date, searchResults.search_time_range, doctor.id)}
                        </div>
                    </div>
                </div>
            `;
        }

        function closeModal() {
            document.getElementById('appointment_modal').classList.add('hidden');
            
            // Resetear formulario del modal
            document.getElementById('appointment_form').reset();
        }

        function resetSearch() {
            // Ocultar resultados
            document.getElementById('search_results').classList.add('hidden');
            document.getElementById('no_results').classList.add('hidden');
            
            // Limpiar formulario de resumen
            document.getElementById('summary_doctor_display').textContent = 'Dr. --';
            document.getElementById('summary_doctor_id').value = '';
            document.getElementById('summary_date').value = '';
            document.getElementById('summary_date_display').textContent = '--';
            document.getElementById('summary_time_display').textContent = '--';
            document.getElementById('summary_start_time').value = '';
            document.getElementById('summary_end_time').value = '';
            document.querySelector('#appointment_summary_form select[name="patient_id"]').value = '';
            document.querySelector('#appointment_summary_form textarea[name="reason"]').value = '';
            
            // Resetear duración a valor por defecto
            document.querySelector('#appointment_summary_form input[name="duration"]').value = '15';
            
            // Deshabilitar botón de confirmar
            document.getElementById('confirm_appointment_btn').disabled = true;
            
            // Limpiar campos de búsqueda
            document.getElementById('search_time_range').value = '';
            document.getElementById('search_speciality').value = '';
            
            // Scroll arriba
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Manejar envío del formulario del resumen
        document.getElementById('appointment_summary_form').addEventListener('submit', function(e) {
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Guardando...';
            submitBtn.disabled = true;
            
            // El formulario se enviará normalmente
        });
    </script>
</x-admin-layout>
