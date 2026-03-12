<x-admin-layout title="Horarios del Doctor | MediLink" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Doctores',
        'href' => route('admin.doctors.index')
    ],
    [
        'name' => 'Horarios',
    ],
]">
    <div class="w-full">
        <!-- Header del doctor -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center">
                        @php
                            $nameParts = explode(' ', $doctor->user->name);
                            $initials = count($nameParts) > 1 ? $nameParts[0][0] . $nameParts[1][0] : substr($nameParts[0], 0, 2);
                        @endphp
                        <span class="text-indigo-500 font-bold text-xl">{{ $initials }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dr. {{ $doctor->user->name }}</h1>
                        <p class="text-indigo-500">{{ $doctor->speciality->name ?? 'General' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.doctors.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Volver a la lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Section Gestor de Horarios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Gestor de horarios</h2>
                </div>
                <!-- Action button -->
                <button type="button" 
                        onclick="saveBulkSchedule()"
                        id="save_schedule_btn"
                        class="px-6 py-2.5 bg-indigo-500 text-white font-medium text-sm rounded-lg hover:bg-indigo-600 transition-colors shadow-sm">
                    Guardar horario
                </button>
            </div>
            
            <div class="overflow-x-auto p-6">
                <div class="min-w-[1000px]">
                    <!-- Table Header -->
                    <div class="grid grid-cols-7 border-b border-gray-200 pb-4 mb-4 text-sm font-semibold text-gray-500 uppercase tracking-wider">
                        <div class="col-span-1 pl-4">DÍA/HORA</div>
                        @php
                            $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                        @endphp
                        @foreach($days as $day)
                            <div class="col-span-1 text-center">{{ $day }}</div>
                        @endforeach
                    </div>

                    <!-- Grid Rows -->
                    <div id="schedule_grid_body" class="space-y-6">
                        @for($hour = 8; $hour <= 20; $hour++)
                        <div class="grid grid-cols-7 border-b border-gray-100 pb-6 items-start">
                            <!-- Hour Label -->
                            <div class="col-span-1 pl-4 flex items-start pt-2">
                                <label class="flex items-center text-sm font-medium text-gray-900 cursor-pointer">
                                    <input type="checkbox" 
                                           class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mr-3 hidden" 
                                           onchange="toggleEntireRow(this, {{ $hour }})">
                                    {{ sprintf('%02d:00:00', $hour) }}
                                </label>
                            </div>
                            
                            <!-- Day Columns -->
                            @foreach($days as $day)
                            <div class="col-span-1 px-2 space-y-2 text-sm" data-day="{{ $day }}" data-hour="{{ $hour }}">
                                <!-- Checkbox "Todos" -->
                                <label class="flex items-center cursor-pointer text-gray-700 mb-3 hover:text-indigo-600">
                                    <input type="checkbox" 
                                           class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mr-2 toggle-all-btn"
                                           onchange="toggleColumnInRow(this, '{{ $day }}', {{ $hour }})">
                                    Todos
                                </label>
                                
                                <!-- Intervalos 15 min -->
                                @for($min = 0; $min < 60; $min += 15)
                                    @php
                                        $startTimeStr = sprintf('%02d:%02d', $hour, $min);
                                        $endMin = $min + 15;
                                        $endHour = $hour;
                                        if($endMin >= 60) {
                                            $endMin = 0;
                                            $endHour++;
                                        }
                                        $endTimeStr = sprintf('%02d:%02d', $endHour, $endMin);
                                    @endphp
                                    <label class="flex items-center cursor-pointer text-gray-600 hover:text-indigo-600">
                                        <input type="checkbox" 
                                               value="{{ $startTimeStr }}-{{ $endTimeStr }}"
                                               class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mr-2 interval-cb"
                                               onchange="checkIfAllSelected('{{ $day }}', {{ $hour }})">
                                        {{ $startTimeStr }} - {{ $endTimeStr }}
                                    </label>
                                @endfor
                            </div>
                            @endforeach
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden data to load on init -->
    <script>
        const existingSchedules = @json($schedules);
        const doctorId = {{ $doctor->id }};
        const bulkUpdateUrl = '{{ route("admin.doctors.schedule.bulkUpdate", $doctor) }}';
        const csrfToken = '{{ csrf_token() }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Load existing schedules into DOM
            existingSchedules.forEach(schedule => {
                const day = schedule.day;
                const startStr = schedule.start_time.substring(0, 5); // "08:00"
                const endStr = schedule.end_time.substring(0, 5); // "10:00"

                // We need to check all 15 min blocks between startStr and endStr
                checkIntervalsForDay(day, startStr, endStr);
            });

            // Calculate "Todos" for all day/hour blocks
            const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            for(let hr = 8; hr <= 20; hr++) {
                days.forEach(day => {
                    checkIfAllSelectedState(day, hr);
                });
            }
        });

        // Parse time to minutes (e.g. 08:30 -> 510)
        function timeToMinutes(timeStr) {
            const [h, m] = timeStr.split(':').map(Number);
            return h * 60 + m;
        }

        // Iterate intervals and check them
        function checkIntervalsForDay(day, startStr, endStr) {
            const startMins = timeToMinutes(startStr);
            const endMins = timeToMinutes(endStr);

            const dayContainers = document.querySelectorAll(`div[data-day="${day}"]`);
            dayContainers.forEach(container => {
                const checkboxes = container.querySelectorAll('.interval-cb');
                checkboxes.forEach(cb => {
                    const [cbStart, cbEnd] = cb.value.split('-');
                    const cbStartMins = timeToMinutes(cbStart);
                    const cbEndMins = timeToMinutes(cbEnd);

                    if (cbStartMins >= startMins && cbEndMins <= endMins) {
                        cb.checked = true;
                    }
                });
                
                // Update "Todos" if necessary
                checkIfAllSelected(day, container.dataset.hour);
            });
        }

        // Toggle all checkboxes in a specific day/hour block
        function toggleColumnInRow(source, day, hour) {
            const container = document.querySelector(`div[data-day="${day}"][data-hour="${hour}"]`);
            if (container) {
                const checkboxes = container.querySelectorAll('.interval-cb');
                checkboxes.forEach(cb => {
                    cb.checked = source.checked;
                });
            }
        }

        // Update "Todos" state logic without event object
        function checkIfAllSelectedState(day, hour) {
            const container = document.querySelector(`div[data-day="${day}"][data-hour="${hour}"]`);
            if (container) {
                const checkboxes = container.querySelectorAll('.interval-cb');
                if (checkboxes.length > 0) {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    const toggleBtn = container.querySelector('.toggle-all-btn');
                    if (toggleBtn) toggleBtn.checked = allChecked;
                }
            }
        }

        // Update "Todos" state if manual checking finishes row
        function checkIfAllSelected(day, hour) {
            checkIfAllSelectedState(day, hour);
        }

        function toggleEntireRow(source, hour) {
            const containers = document.querySelectorAll(`div[data-hour="${hour}"]`);
            containers.forEach(container => {
                const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    cb.checked = source.checked;
                });
            });
        }

        function saveBulkSchedule() {
            const btn = document.getElementById('save_schedule_btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Guardando...';
            btn.disabled = true;

            const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            const allSchedules = [];

            days.forEach(day => {
                const dayContainers = document.querySelectorAll(`div[data-day="${day}"]`);
                let dayIntervals = [];

                dayContainers.forEach(container => {
                    const checkboxes = container.querySelectorAll('.interval-cb:checked');
                    checkboxes.forEach(cb => {
                        const [start, end] = cb.value.split('-');
                        dayIntervals.push({ startMins: timeToMinutes(start), endMins: timeToMinutes(end), start, end });
                    });
                });

                // Sort and merge contiguous intervals
                if (dayIntervals.length > 0) {
                    dayIntervals.sort((a, b) => a.startMins - b.startMins);
                    
                    let currentBlock = { ...dayIntervals[0] };
                    for (let i = 1; i < dayIntervals.length; i++) {
                        const nextBlock = dayIntervals[i];
                        if (currentBlock.endMins === nextBlock.startMins) { // Contiguous
                            currentBlock.endMins = nextBlock.endMins;
                            currentBlock.end = nextBlock.end;
                        } else {
                            // Push and start new block
                            allSchedules.push({ day, start_time: currentBlock.start, end_time: currentBlock.end });
                            currentBlock = { ...nextBlock };
                        }
                    }
                    allSchedules.push({ day, start_time: currentBlock.start, end_time: currentBlock.end });
                }
            });

            console.log('Sending payload:', allSchedules);

            fetch(bulkUpdateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ schedules: allSchedules })
            })
            .then(res => {
                if(!res.ok) throw new Error('Error de red');
                return res.json();
            })
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: 'El horario ha sido actualizado con éxito.',
                        confirmButtonColor: '#4F46E5',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Ocurrió un problema al guardar los horarios.',
                    confirmButtonColor: '#4F46E5'
                });
                btn.innerHTML = 'Guardar horario';
                btn.disabled = false;
            });
        }
    </script>
</x-admin-layout>
