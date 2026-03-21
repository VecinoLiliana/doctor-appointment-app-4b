const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

class WhatsAppSender {
    constructor() {
        this.browser = null;
        this.page = null;
        this.isConnected = false;
    }

    async initialize() {
        if (this.browser) return;

        const userDataDir = path.join(process.env.HOME || process.env.USERPROFILE, 'whatsapp-session');
        
        this.browser = await puppeteer.launch({
            headless: false, // Mostrar navegador para escanear QR
            userDataDir: userDataDir,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--single-process',
                '--disable-gpu'
            ]
        });

        this.page = await this.browser.newPage();
        
        // Configurar viewport
        await this.page.setViewport({ width: 1280, height: 720 });
        
        // Ir a WhatsApp Web
        await this.page.goto('https://web.whatsapp.com', {
            waitUntil: 'networkidle2',
            timeout: 60000
        });

        // Esperar a que se cargue
        await this.page.waitForSelector('canvas[aria-label="Scan me!"]', { timeout: 30000 })
            .then(() => console.log('QR code detected - please scan'))
            .catch(() => console.log('Already logged in or QR not found'));

        // Esperar a que se conecte
        await this.waitForConnection();
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
            console.log('WhatsApp connected successfully');
            
        } catch (error) {
            console.log('Connection timeout or failed');
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
            
            // Abrir chat
            await this.page.goto(`https://web.whatsapp.com/send?phone=${formattedPhone}&text=${encodeURIComponent(message)}`, {
                waitUntil: 'networkidle2'
            });

            // Esperar a que cargue el chat
            await this.page.waitForSelector('[data-testid="conversation-panel"]', { timeout: 30000 });

            // Esperar un momento para que el mensaje se cargue
            await this.page.waitForTimeout(2000);

            // Encontrar y hacer clic en el botón de enviar
            const sendButton = await this.page.waitForSelector('[data-testid="send"]', { timeout: 10000 });
            
            if (sendButton) {
                await sendButton.click();
                
                // Esperar a que se envíe
                await this.page.waitForTimeout(3000);
                
                console.log('Message sent successfully to', phone);
                return { success: true, message: 'Message sent' };
            } else {
                throw new Error('Send button not found');
            }

        } catch (error) {
            console.error('Error sending message:', error);
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
        if (this.browser) {
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
    const sender = new WhatsAppSender();

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
        // await sender.close();
    }
}

// Ejecutar
main().catch(console.error);
