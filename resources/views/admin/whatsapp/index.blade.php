<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Configuración de WhatsApp</h1>
                    <p class="text-gray-600 mt-1">Gestiona el envío de mensajes de confirmación y recordatorios</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="check_connection_btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fa-solid fa-sync-alt mr-2"></i>Verificar Conexión
                    </button>
                    <button id="send_test_btn" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Enviar Prueba
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado de Conexión -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado de Conexión</h2>
            
            <div id="connection_status" class="flex items-center space-x-4">
                <div id="status_indicator" class="w-3 h-3 rounded-full bg-gray-400"></div>
                <span id="status_text" class="text-gray-600">Verificando...</span>
            </div>

            <!-- QR Code Section -->
            <div id="qr_section" class="hidden mt-6">
                <div class="border-t pt-6">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Escanea este código QR con WhatsApp</h3>
                    <div class="flex items-center space-x-6">
                        <div id="qr_code_container" class="w-64 h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-gray-500">Cargando QR...</span>
                        </div>
                        <div class="flex-1">
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                                <li>Abre WhatsApp en tu teléfono</li>
                                <li>Toca "Configuración" → "WhatsApp Web"</li>
                                <li>Escanea el código QR con tu cámara</li>
                                <li>Espera a que aparezca "Conectado"</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recordatorios -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recordatorios Automáticos</h2>
                <p class="text-gray-600 text-sm mb-4">
                    Envía recordatorios automáticamente un día antes de cada cita
                </p>
                <button id="send_reminders_btn" class="w-full px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                    <i class="fa-solid fa-bell mr-2"></i>Enviar Recordatorios Ahora
                </button>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Programado para ejecutarse automáticamente todos los días a las 9:00 AM
                    </p>
                </div>
            </div>

            <!-- Mensaje de Prueba -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Mensaje de Prueba</h2>
                <form id="test_form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" id="test_phone" placeholder="5512345678" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                        <textarea name="message" id="test_message" rows="3" placeholder="Escribe un mensaje de prueba..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Enviar Mensaje de Prueba
                    </button>
                </form>
            </div>
        </div>

        <!-- Logs -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h2>
            <div id="activity_log" class="space-y-2 max-h-64 overflow-y-auto">
                <div class="text-gray-500 text-sm">No hay actividad reciente</div>
            </div>
        </div>
    </div>

    <!-- Test Modal -->
    <div id="test_modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Enviar Mensaje de Prueba</h3>
                <form id="modal_test_form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" placeholder="5512345678" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                        <textarea name="message" rows="3" placeholder="Escribe un mensaje de prueba..." required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            Enviar
                        </button>
                        <button type="button" onclick="closeTestModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Estado global
        let isConnected = false;
        let checkInterval = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            checkConnection();
            startAutoCheck();
        });

        // Verificar conexión
        function checkConnection() {
            fetch('/admin/whatsapp/check-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                updateConnectionStatus(data.connected);
                
                if (!data.connected) {
                    getQRCode();
                }
            })
            .catch(error => {
                console.error('Error checking connection:', error);
                updateConnectionStatus(false);
            });
        }

        // Obtener QR Code
        function getQRCode() {
            fetch('/admin/whatsapp/qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.qr) {
                    document.getElementById('qr_code_container').innerHTML = `
                        <img src="${data.qr}" alt="QR Code" class="w-full h-full object-contain">
                    `;
                    document.getElementById('qr_section').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error getting QR:', error);
            });
        }

        // Actualizar estado de conexión
        function updateConnectionStatus(connected) {
            isConnected = connected;
            const indicator = document.getElementById('status_indicator');
            const text = document.getElementById('status_text');
            const qrSection = document.getElementById('qr_section');

            if (connected) {
                indicator.className = 'w-3 h-3 rounded-full bg-green-500';
                text.textContent = 'Conectado';
                text.className = 'text-green-600 font-medium';
                qrSection.classList.add('hidden');
            } else {
                indicator.className = 'w-3 h-3 rounded-full bg-red-500';
                text.textContent = 'No conectado';
                text.className = 'text-red-600 font-medium';
            }
        }

        // Auto-check cada 30 segundos
        function startAutoCheck() {
            checkInterval = setInterval(checkConnection, 30000);
        }

        // Botón de verificar conexión
        document.getElementById('check_connection_btn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-sync-alt fa-spin mr-2"></i>Verificando...';
            
            checkConnection();
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fa-solid fa-sync-alt mr-2"></i>Verificar Conexión';
            }, 2000);
        });

        // Botón de enviar prueba
        document.getElementById('send_test_btn').addEventListener('click', function() {
            document.getElementById('test_modal').classList.remove('hidden');
        });

        // Formulario de prueba modal
        document.getElementById('modal_test_form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                phone: formData.get('phone'),
                message: formData.get('message')
            };

            fetch('/admin/whatsapp/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLogEntry('Mensaje de prueba enviado a ' + data.phone, 'success');
                    closeTestModal();
                    this.reset();
                } else {
                    addLogEntry('Error: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error sending test:', error);
                addLogEntry('Error al enviar mensaje de prueba', 'error');
            });
        });

        // Botón de enviar recordatorios
        document.getElementById('send_reminders_btn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-sync-alt fa-spin mr-2"></i>Enviando...';
            
            fetch('/admin/whatsapp/send-reminders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLogEntry('Recordatorios enviados correctamente', 'success');
                } else {
                    addLogEntry('Error enviando recordatorios: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error sending reminders:', error);
                addLogEntry('Error al enviar recordatorios', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fa-solid fa-bell mr-2"></i>Enviar Recordatorios Ahora';
            });
        });

        // Cerrar modal
        function closeTestModal() {
            document.getElementById('test_modal').classList.add('hidden');
        }

        // Agregar entrada al log
        function addLogEntry(message, type = 'info') {
            const log = document.getElementById('activity_log');
            const time = new Date().toLocaleTimeString();
            
            const entry = document.createElement('div');
            entry.className = `flex items-center space-x-2 text-sm p-2 rounded ${
                type === 'success' ? 'bg-green-50 text-green-700' :
                type === 'error' ? 'bg-red-50 text-red-700' :
                'bg-gray-50 text-gray-700'
            }`;
            
            entry.innerHTML = `
                <span class="text-gray-500">${time}</span>
                <span>${message}</span>
            `;
            
            // Agregar al principio
            log.insertBefore(entry, log.firstChild);
            
            // Limitar a 10 entradas
            while (log.children.length > 10) {
                log.removeChild(log.lastChild);
            }
        }

        // Limpiar intervalo al salir
        window.addEventListener('beforeunload', function() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        });
    </script>
</x-admin-layout>
