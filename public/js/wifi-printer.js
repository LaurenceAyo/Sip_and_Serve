/**
 * WiFi Thermal Printer Integration for GOOJPRT PT-210
 */
class WiFiThermalPrinter {
    constructor() {
        this.printerIP = null;
        this.printerPort = 9100;
        this.connected = false;
        this.lastStatusCheck = null;
        
        this.initializeUI();
        this.loadSavedConfig();
        this.checkStatus();
    }

    initializeUI() {
        if (!document.getElementById('wifiPrinterStatus')) {
            this.createWiFiUI();
        }
        this.updateStatus('disconnected', 'WiFi Printer Not Configured');
    }

    createWiFiUI() {
        const container = document.querySelector('.bluetooth-status') || document.body;
        
        const wifiUI = document.createElement('div');
        wifiUI.innerHTML = `
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-wifi"></i> WiFi Printer Status
                    </h6>
                    <div id="wifiPrinterStatus" class="alert alert-secondary mb-3">
                        <span class="printer-status-indicator status-disconnected"></span>
                        <span id="wifiStatusText">WiFi Printer Not Configured</span>
                        <button id="wifiTestBtn" class="btn btn-outline-success btn-sm ms-2">Test Print</button>
                        <button id="wifiConfigBtn" class="btn btn-outline-primary btn-sm ms-1">Configure</button>
                    </div>
                    
                    <div id="wifiConfigPanel" class="border rounded p-3" style="display: none;">
                        <h6>Printer Configuration</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <label for="printerIPInput" class="form-label">Printer IP Address:</label>
                                <input type="text" id="printerIPInput" class="form-control" placeholder="192.168.1.100">
                                <div class="form-text">Find this on your printer's network config printout</div>
                            </div>
                            <div class="col-md-4">
                                <label for="printerPortInput" class="form-label">Port:</label>
                                <input type="number" id="printerPortInput" class="form-control" value="9100" min="1" max="65535">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button id="wifiSaveBtn" class="btn btn-primary btn-sm">Save & Test</button>
                            <button id="wifiCancelBtn" class="btn btn-secondary btn-sm">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(wifiUI);
        this.bindEvents();
    }

    bindEvents() {
        document.getElementById('wifiTestBtn')?.addEventListener('click', () => this.testPrint());
        document.getElementById('wifiConfigBtn')?.addEventListener('click', () => this.showConfig());
        document.getElementById('wifiSaveBtn')?.addEventListener('click', () => this.saveConfig());
        document.getElementById('wifiCancelBtn')?.addEventListener('click', () => this.hideConfig());
    }

    async checkStatus() {
        try {
            const response = await fetch('/cashier/wifi-printer-status', {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': this.getCSRFToken() }
            });

            const data = await response.json();
            
            if (data.success && data.printer_status) {
                const status = data.printer_status;
                this.printerIP = status.printer_ip;
                this.printerPort = status.printer_port;
                this.connected = status.connected;
                
                this.updateStatus(
                    status.connected ? 'connected' : 'disconnected',
                    status.connected ? `Connected: ${status.printer_ip}` : `Disconnected: ${status.printer_ip}`
                );
            }
        } catch (error) {
            console.error('Status check failed:', error);
            this.updateStatus('error', 'Status check failed');
        }
    }

    async printReceipt(orderId) {
        if (!this.connected) {
            alert('WiFi printer not connected. Please configure and test first.');
            return false;
        }

        try {
            const response = await fetch('/cashier/print-wifi-receipt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({ order_id: orderId })
            });

            const data = await response.json();
            
            if (data.success) {
                alert('Receipt printed successfully!');
                return true;
            } else {
                throw new Error(data.message || 'Print failed');
            }
        } catch (error) {
            alert(`Print failed: ${error.message}`);
            return false;
        }
    }

    async testPrint() {
        try {
            this.updateStatus('connecting', 'Testing printer...');
            
            const response = await fetch('/cashier/test-wifi-printer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.connected = true;
                this.updateStatus('connected', `Test successful: ${this.printerIP}`);
                alert('Test print successful! Check your printer.');
            } else {
                this.connected = false;
                this.updateStatus('error', data.message || 'Test failed');
                alert(data.message || 'Test print failed');
            }
        } catch (error) {
            this.connected = false;
            this.updateStatus('error', 'Test failed');
            alert(`Test failed: ${error.message}`);
        }
    }

    showConfig() {
        const panel = document.getElementById('wifiConfigPanel');
        const ipInput = document.getElementById('printerIPInput');
        
        if (this.printerIP) ipInput.value = this.printerIP;
        panel.style.display = 'block';
        ipInput.focus();
    }

    hideConfig() {
        document.getElementById('wifiConfigPanel').style.display = 'none';
    }

    async saveConfig() {
        const ip = document.getElementById('printerIPInput').value.trim();
        const port = parseInt(document.getElementById('printerPortInput').value) || 9100;
        
        if (!this.isValidIP(ip)) {
            alert('Please enter a valid IP address');
            return;
        }

        try {
            const response = await fetch('/cashier/update-wifi-printer-ip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({ printer_ip: ip, printer_port: port })
            });

            const data = await response.json();
            
            if (data.success) {
                this.printerIP = ip;
                this.printerPort = port;
                this.connected = data.connection_test;
                
                this.hideConfig();
                this.updateStatus(
                    this.connected ? 'connected' : 'disconnected',
                    this.connected ? `Connected: ${ip}` : `Saved: ${ip} (not reachable)`
                );
                
                alert('Printer configuration saved!');
            } else {
                throw new Error(data.message || 'Failed to save configuration');
            }
        } catch (error) {
            alert(`Save failed: ${error.message}`);
        }
    }

    updateStatus(type, message) {
        const statusEl = document.getElementById('wifiPrinterStatus');
        const textEl = document.getElementById('wifiStatusText');
        
        if (!statusEl || !textEl) return;

        textEl.textContent = message;
        statusEl.className = 'alert mb-3';
        
        switch (type) {
            case 'connected': statusEl.classList.add('alert-success'); break;
            case 'connecting': statusEl.classList.add('alert-warning'); break;
            case 'disconnected': statusEl.classList.add('alert-secondary'); break;
            case 'error': statusEl.classList.add('alert-danger'); break;
        }
    }

    isValidIP(ip) {
        const ipRegex = /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        return ipRegex.test(ip);
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    isConnected() {
        return this.connected;
    }

    loadSavedConfig() {
        try {
            const saved = localStorage.getItem('wifi_printer_config');
            if (saved) {
                const config = JSON.parse(saved);
                this.printerIP = config.ip;
                this.printerPort = config.port || 9100;
            }
        } catch (error) {
            console.warn('Failed to load config:', error);
        }
    }
}

// Initialize WiFi printer and make it global
let wifiPrinter;
document.addEventListener('DOMContentLoaded', function() {
    wifiPrinter = new WiFiThermalPrinter();
    window.wifiPrinter = wifiPrinter; // Make it globally accessible
});

// Global print function
function printWiFiReceipt(orderId) {
    if (window.wifiPrinter && window.wifiPrinter.isConnected()) {
        window.wifiPrinter.printReceipt(orderId);
    } else {
        alert('WiFi printer not connected. Please configure and test first.');
    }
}

// Make function globally accessible
window.printWiFiReceipt = printWiFiReceipt;