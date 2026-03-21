const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

class WhatsAppRealSender {
    constructor() {
        this.browser = null;
        this.page = null;
        this.isConnected = false;
        this.logFile = path.join(process.cwd(), 'storage/logs/whatsapp.log');
        this.ensureLogDirectory();
    }

    ensureLogDirectory() {
        const logDir = path.dirname(this.logFile);
        if (!fs.existsSync(logDir)) {
            fs.mkdirSync(logDir, { recursive: true });
        }
    }

    logMessage(message) {
        const timestamp = new Date().toISOString();
        const logEntry = `[${timestamp}] ${message}\n`;
        fs.appendFileSync(this.logFile, logEntry);
        console.log(logEntry.trim());
    }

    async initialize() {
        if (this.browser && this.browser.isConnected()) {
            return;
        }

        try {
            // Conectar a una instancia existente de Chrome si está disponible
            try {
                this.browser = await puppeteer.connect({
                    browserURL: 'http://localhost:9222',
                    defaultViewport: null
                });
                this.logMessage('Conectado a instancia existente de Chrome');
            } catch (connectError) {
                // Si no hay instancia existente, crear una nueva
                this.logMessage('No se encontró instancia existente, creando nueva...');
                
                const userDataDir = path.join(process.cwd(), 'storage/app/whatsapp-session');
                
                this.browser = await puppeteer.launch({
                    headless: false, // Mostrar navegador para escanear QR
                    userDataDir: userDataDir,
                    args: [
                        '--no-sandbox',
                        '--disable-setuid-sandbox',
                        '--disable-dev-shm-usage',
                        '--disable-accelerated-2d-canvas',
                        '--no-first-run',
                        '--disable-gpu',
                        '--window-size=1280,720',
                        '--remote-debugging-port=9222' // Habilitar debugging
                    ]
                });
            }

            // Buscar o crear una página que tenga WhatsApp Web
            const pages = await this.browser.pages();
            this.page = pages.find(page => page.url().includes('web.whatsapp.com'));
            
            if (!this.page) {
                this.page = await this.browser.newPage();
                await this.page.setViewport({ width: 1280, height: 720 });
                
                // Ir a WhatsApp Web
                await this.page.goto('https://web.whatsapp.com', {
                    waitUntil: 'networkidle2',
                    timeout: 60000
                });
            }

            this.logMessage('WhatsApp Web iniciado - verificando conexión...');

            // Esperar un momento para que cargue
            await new Promise(resolve => setTimeout(resolve, 3000));

            // Verificar si ya está conectado o necesita QR
            const needsQR = await this.page.evaluate(() => {
                const qrCanvas = document.querySelector('canvas[aria-label="Scan me!"]');
                return qrCanvas !== null;
            });

            if (needsQR) {
                this.logMessage('QR code detectado - por favor escanea con tu teléfono');
                // Esperar a que se escanee el QR
                await this.waitForConnection();
            } else {
                this.logMessage('Ya conectado a WhatsApp');
                this.isConnected = true;
            }

        } catch (error) {
            this.logMessage(`Error inicializando WhatsApp: ${error.message}`);
            throw error;
        }
    }

    async waitForConnection() {
        try {
            // Esperar a que aparezca el panel principal (indicador de conexión)
            await this.page.waitForFunction(() => {
                const mainPanel = document.querySelector('[data-testid="panel"]');
                const sidePanel = document.querySelector('[data-testid="side"]');
                return mainPanel && sidePanel;
            }, { timeout: 120000 }); // 2 minutos timeout

            this.isConnected = true;
            this.logMessage('WhatsApp conectado exitosamente');
            
        } catch (error) {
            this.logMessage('Tiempo de espera agotado - no se pudo conectar');
            this.isConnected = false;
            throw error;
        }
    }

    async sendMessage(phone, message) {
        try {
            if (!this.isConnected) {
                await this.initialize();
            }

            // Formatear número para WhatsApp
            const formattedPhone = phone + '@c.us';
            
            this.logMessage(`Enviando mensaje a ${phone}: ${message.substring(0, 50)}...`);
            
            // Abrir chat
            await this.page.goto(`https://web.whatsapp.com/send?phone=${formattedPhone}&text=${encodeURIComponent(message)}`, {
                waitUntil: 'networkidle2'
            });

            // Esperar a que cargue el chat
            await this.page.waitForSelector('[data-testid="conversation-panel"]', { timeout: 30000 });

            // Esperar un momento para que el mensaje se cargue
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Encontrar y hacer clic en el botón de enviar
            const sendButton = await this.page.waitForSelector('[data-testid="send"]', { timeout: 10000 });
            
            if (sendButton) {
                await sendButton.click();
                
                // Esperar a que se envíe
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                this.logMessage(`Mensaje enviado exitosamente a ${phone}`);
                return { success: true, message: 'Message sent successfully' };
            } else {
                throw new Error('Send button not found');
            }

        } catch (error) {
            this.logMessage(`Error enviando mensaje a ${phone}: ${error.message}`);
            return { success: false, error: error.message };
        }
    }

    async checkConnection() {
        try {
            if (!this.page) {
                await this.initialize();
            }

            const isConnected = await this.page.evaluate(() => {
                const mainPanel = document.querySelector('[data-testid="panel"]');
                const sidePanel = document.querySelector('[data-testid="side"]');
                return mainPanel && sidePanel;
            });

            this.isConnected = isConnected;
            return { success: true, connected: isConnected };

        } catch (error) {
            this.isConnected = false;
            return { success: false, connected: false, error: error.message };
        }
    }

    async getQRCode() {
        try {
            if (!this.page) {
                await this.initialize();
            }

            const qrData = await this.page.evaluate(() => {
                const canvas = document.querySelector('canvas[aria-label="Scan me!"]');
                if (canvas) {
                    return canvas.toDataURL();
                }
                return null;
            });

            return { success: true, qr: qrData };

        } catch (error) {
            return { success: false, error: error.message };
        }
    }

    async close() {
        if (this.browser && this.browser.isConnected()) {
            await this.browser.close();
            this.browser = null;
            this.page = null;
            this.isConnected = false;
        }
    }
}

// Main execution
async function main() {
    const whatsappData = JSON.parse(process.env.WHATSAPP_DATA || '{}');
    const sender = new WhatsAppRealSender();

    try {
        switch (whatsappData.action) {
            case 'checkConnection':
                const connectionResult = await sender.checkConnection();
                console.log(JSON.stringify(connectionResult));
                break;

            case 'getQR':
                const qrResult = await sender.getQRCode();
                console.log(JSON.stringify(qrResult));
                break;

            default:
                // Enviar mensaje
                const result = await sender.sendMessage(whatsappData.phone, whatsappData.message);
                console.log(JSON.stringify(result));
                break;
        }

    } catch (error) {
        console.log(JSON.stringify({ success: false, error: error.message }));
    } finally {
        // No cerrar el navegador para mantener la sesión
    }
}

// Ejecutar
main().catch(console.error);
