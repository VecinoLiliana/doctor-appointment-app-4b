const fs = require('fs');
const path = require('path');

class WhatsAppSimulator {
    constructor() {
        this.logFile = path.join(process.cwd(), 'storage/logs/whatsapp.log');
        this.ensureLogDirectory();
    }

    ensureLogDirectory() {
        const logDir = path.dirname(this.logFile);
        if (!fs.existsSync(logDir)) {
            fs.mkdirSync(logDir, { recursive: true });
        }
    }

    logMessage(phone, message, type = 'sent') {
        const timestamp = new Date().toISOString();
        const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${phone} - ${message}\n`;
        
        fs.appendFileSync(this.logFile, logEntry);
        console.log(logEntry.trim());
    }

    async sendMessage(phone, message) {
        try {
            // Simular envío exitoso
            this.logMessage(phone, message, 'sent');
            
            // Simular tiempo de envío (muy corto)
            await new Promise(resolve => setTimeout(resolve, 100));
            
            return { success: true, message: 'Message sent successfully (simulated)' };
        } catch (error) {
            this.logMessage(phone, `Error: ${error.message}`, 'error');
            return { success: false, error: error.message };
        }
    }

    async checkConnection() {
        // Simular que siempre está conectado
        return { success: true, connected: true };
    }

    async getQRCode() {
        // No necesitamos QR en modo simulado
        return { success: true, qr: null };
    }
}

// Main execution
async function main() {
    const whatsappData = JSON.parse(process.env.WHATSAPP_DATA || '{}');
    const simulator = new WhatsAppSimulator();

    try {
        switch (whatsappData.action) {
            case 'checkConnection':
                const connectionResult = await simulator.checkConnection();
                console.log(JSON.stringify(connectionResult));
                break;

            case 'getQR':
                const qrResult = await simulator.getQRCode();
                console.log(JSON.stringify(qrResult));
                break;

            default:
                // Enviar mensaje
                const result = await simulator.sendMessage(whatsappData.phone, whatsappData.message);
                console.log(JSON.stringify(result));
                break;
        }

    } catch (error) {
        console.log(JSON.stringify({ success: false, error: error.message }));
    }
}

// Ejecutar
main().catch(console.error);
