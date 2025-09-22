/**
 * Web Bluetooth Thermal Printer Integration for Cashier System
 * Compatible with GOOJPRT PT-210 and similar ESC/POS printers
 */
class BluetoothThermalPrinter {
    constructor() {
        this.device = null;
        this.characteristic = null;
        this.connected = false;
        this.connecting = false;
        this.serviceUuid = '000018f0-0000-1000-8000-00805f9b34fb';
        this.characteristicUuid = '00002af1-0000-1000-8000-00805f9b34fb';
        
        this.initializeUI();
        this.checkBrowserSupport();
    }

    initializeUI() {
        // Create Bluetooth printer UI if not exists
        if (!document.getElementById('bluetoothPrinterStatus')) {
            this.createBluetoothUI();
        }
        this.updateStatus('disconnected', 'Bluetooth Printer Disconnected');
    }

    createBluetoothUI() {
        const container = document.querySelector('.printer-status') || document.body;
        
        const bluetoothUI = document.createElement('div');
        bluetoothUI.innerHTML = `
            <div id="bluetoothPrinterStatus" class="alert alert-secondary mb-3">
                <i class="fas fa-bluetooth"></i> <span id="bluetoothStatusText">Bluetooth Printer Disconnected</span>
                <button id="bluetoothConnectBtn" class="btn btn-primary btn-sm ms-2">Connect</button>
                <button id="bluetoothDisconnectBtn" class="btn btn-secondary btn-sm ms-1" style="display: none;">Disconnect</button>
            </div>
        `;
        
        container.appendChild(bluetoothUI);
        
        // Bind events
        document.getElementById('bluetoothConnectBtn').addEventListener('click', () => this.connect());
        document.getElementById('bluetoothDisconnectBtn').addEventListener('click', () => this.disconnect());
    }

    checkBrowserSupport() {
        if (!navigator.bluetooth) {
            this.updateStatus('error', 'Web Bluetooth not supported. Use Chrome or Edge browser.');
            document.getElementById('bluetoothConnectBtn').disabled = true;
            return false;
        }
        return true;
    }

    updateStatus(type, message) {
        const statusEl = document.getElementById('bluetoothPrinterStatus');
        const textEl = document.getElementById('bluetoothStatusText');
        const connectBtn = document.getElementById('bluetoothConnectBtn');
        const disconnectBtn = document.getElementById('bluetoothDisconnectBtn');
        
        if (!statusEl || !textEl) return;

        textEl.textContent = message;
        
        // Reset classes
        statusEl.className = 'alert mb-3';
        
        switch (type) {
            case 'connected':
                statusEl.classList.add('alert-success');
                connectBtn.style.display = 'none';
                disconnectBtn.style.display = 'inline-block';
                break;
            case 'connecting':
                statusEl.classList.add('alert-warning');
                connectBtn.disabled = true;
                disconnectBtn.style.display = 'none';
                break;
            case 'disconnected':
                statusEl.classList.add('alert-secondary');
                connectBtn.style.display = 'inline-block';
                connectBtn.disabled = false;
                disconnectBtn.style.display = 'none';
                break;
            case 'error':
                statusEl.classList.add('alert-danger');
                connectBtn.disabled = true;
                disconnectBtn.style.display = 'none';
                break;
        }
    }

    async connect() {
        if (this.connecting || this.connected) return;
        
        try {
            this.connecting = true;
            this.updateStatus('connecting', 'Searching for Bluetooth printer...');
            
            console.log('Requesting Bluetooth device...');
            
            // Request device with multiple name filters for GOOJPRT PT-210
            this.device = await navigator.bluetooth.requestDevice({
                filters: [
                    { namePrefix: 'PT-210' },
                    { namePrefix: 'GOOJPRT' },
                    { namePrefix: 'Printer' },
                    { namePrefix: 'POS' },
                    { namePrefix: 'Thermal' }
                ],
                optionalServices: [this.serviceUuid]
            });

            console.log('Device selected:', this.device.name);
            this.updateStatus('connecting', `Connecting to ${this.device.name}...`);

            // Connect to GATT server
            const server = await this.device.gatt.connect();
            console.log('GATT server connected');

            // Get service
            const service = await server.getPrimaryService(this.serviceUuid);
            console.log('Service found');

            // Get characteristic
            this.characteristic = await service.getCharacteristic(this.characteristicUuid);
            console.log('Characteristic found');

            this.connected = true;
            this.connecting = false;
            
            this.updateStatus('connected', `Connected: ${this.device.name}`);
            
            // Handle disconnection
            this.device.addEventListener('gattserverdisconnected', () => {
                console.log('Device disconnected');
                this.onDisconnected();
            });

            // Show success message
            this.showMessage('Bluetooth printer connected successfully!', 'success');
            
        } catch (error) {
            console.error('Bluetooth connection failed:', error);
            this.connecting = false;
            this.onDisconnected();
            this.showMessage(`Connection failed: ${error.message}`, 'error');
        }
    }

    async disconnect() {
        if (this.device && this.device.gatt.connected) {
            await this.device.gatt.disconnect();
        }
        this.onDisconnected();
    }

    onDisconnected() {
        this.connected = false;
        this.connecting = false;
        this.device = null;
        this.characteristic = null;
        this.updateStatus('disconnected', 'Bluetooth Printer Disconnected');
    }

    /**
     * Print receipt using Web Bluetooth
     */
    async printReceipt(orderId = null) {
        if (!this.connected || !this.characteristic) {
            this.showMessage('Bluetooth printer not connected', 'error');
            return false;
        }

        try {
            console.log('Getting receipt content for order:', orderId);
            
            // Get receipt content from server
            const response = await fetch('/cashier/receipt-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    order_id: orderId,
                    type: orderId ? 'order' : 'latest'
                })
            });

            const data = await response.json();
            
            if (!data.success || !data.content) {
                throw new Error('Failed to get receipt content from server');
            }

            // Print the receipt
            await this.printText(data.content);
            
            this.showMessage('Receipt printed successfully!', 'success');
            return true;
            
        } catch (error) {
            console.error('Print failed:', error);
            this.showMessage(`Print failed: ${error.message}`, 'error');
            return false;
        }
    }

    /**
     * Print test receipt
     */
    async printTest() {
        if (!this.connected || !this.characteristic) {
            this.showMessage('Bluetooth printer not connected', 'error');
            return false;
        }

        try {
            // Request test receipt from server
            const response = await fetch('/cashier/web-bluetooth-test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error('Failed to prepare test receipt');
            }

            // Get and print test content
            const contentResponse = await fetch('/cashier/receipt-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: 'test' })
            });

            const contentData = await contentResponse.json();
            
            if (contentData.success && contentData.content) {
                await this.printText(contentData.content);
                this.showMessage('Test receipt printed successfully!', 'success');
                return true;
            } else {
                throw new Error('Failed to get test receipt content');
            }
            
        } catch (error) {
            console.error('Test print failed:', error);
            this.showMessage(`Test print failed: ${error.message}`, 'error');
            return false;
        }
    }

    /**
     * Print raw text using ESC/POS commands
     */
    async printText(text) {
        if (!this.connected || !this.characteristic) {
            throw new Error('Printer not connected');
        }

        try {
            const commands = this.createESCPOSCommands(text);
            
            // Send in chunks to avoid overwhelming the characteristic
            const chunkSize = 20; // bytes per chunk
            for (let i = 0; i < commands.length; i += chunkSize) {
                const chunk = commands.slice(i, i + chunkSize);
                await this.characteristic.writeValue(chunk);
                
                // Small delay between chunks
                await new Promise(resolve => setTimeout(resolve, 50));
            }
            
            console.log('Print data sent successfully');
            
        } catch (error) {
            console.error('Failed to send print data:', error);
            throw error;
        }
    }

    /**
     * Create ESC/POS commands for GOOJPRT PT-210
     */
    createESCPOSCommands(text) {
        const encoder = new TextEncoder();
        const commands = [];
        
        // Initialize printer
        commands.push(new Uint8Array([0x1B, 0x40])); // ESC @
        
        // Set character spacing
        commands.push(new Uint8Array([0x1B, 0x20, 0x00])); // ESC SP 0
        
        // Set line spacing
        commands.push(new Uint8Array([0x1B, 0x33, 0x20])); // ESC 3 32
        
        // Add text content
        commands.push(encoder.encode(text));
        
        // Feed lines and cut
        commands.push(new Uint8Array([0x1B, 0x64, 0x05])); // ESC d 5 (feed 5 lines)
        commands.push(new Uint8Array([0x1D, 0x56, 0x41, 0x00])); // GS V A 0 (full cut)
        
        // Combine all commands
        const totalLength = commands.reduce((sum, cmd) => sum + cmd.length, 0);
        const result = new Uint8Array(totalLength);
        let offset = 0;
        
        for (const cmd of commands) {
            result.set(cmd, offset);
            offset += cmd.length;
        }
        
        return result;
    }

    /**
     * Show message to user
     */
    showMessage(message, type = 'info') {
        // Try to use existing notification system or create a simple alert
        if (typeof showAlert === 'function') {
            showAlert(message, type);
        } else if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            // Fallback to browser alert
            alert(message);
        }
    }

    /**
     * Check if printer is connected
     */
    isConnected() {
        return this.connected;
    }
}

// Initialize Bluetooth printer when page loads
let bluetoothPrinter;
document.addEventListener('DOMContentLoaded', function() {
    bluetoothPrinter = new BluetoothThermalPrinter();
});

// Enhanced acceptOrder function with Bluetooth printing support
function enhancedAcceptOrder(orderId, printBluetooth = false) {
    const cashAmountInput = document.getElementById(`cashAmount_${orderId}`);
    const cashAmount = parseFloat(cashAmountInput.value);
    
    if (!cashAmount || cashAmount <= 0) {
        alert('Please enter a valid cash amount');
        return;
    }

    // Show loading state
    const acceptBtn = document.querySelector(`button[onclick="acceptOrder(${orderId})"]`);
    const originalText = acceptBtn.innerHTML;
    acceptBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    acceptBtn.disabled = true;

    // Send accept order request
    fetch('/cashier/accept-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: orderId,
            cash_amount: cashAmount,
            print_receipt: true,
            open_drawer: true
        })
    })
    .then(response => response.json())
    .then(async data => {
        if (data.success) {
            // If Bluetooth printer is connected and user wants Bluetooth printing
            if (printBluetooth && bluetoothPrinter && bluetoothPrinter.isConnected()) {
                await bluetoothPrinter.printReceipt(orderId);
            }
            
            // Show success message
            alert(`Order accepted! Change: $${data.change_amount.toFixed(2)}`);
            
            // Refresh orders
            if (typeof refreshOrders === 'function') {
                refreshOrders();
            }
        } else {
            alert('Failed to accept order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error accepting order:', error);
        alert('Error accepting order');
    })
    .finally(() => {
        // Restore button
        acceptBtn.innerHTML = originalText;
        acceptBtn.disabled = false;
    });
}

// Add Bluetooth print button to order cards
function addBluetoothPrintButtons() {
    const orderCards = document.querySelectorAll('.order-card');
    
    orderCards.forEach(card => {
        if (card.querySelector('.bluetooth-print-btn')) return; // Already added
        
        const orderId = card.dataset.orderId;
        const buttonContainer = card.querySelector('.btn-group') || card.querySelector('.card-footer');
        
        if (buttonContainer) {
            const bluetoothBtn = document.createElement('button');
            bluetoothBtn.className = 'btn btn-info btn-sm bluetooth-print-btn';
            bluetoothBtn.innerHTML = '<i class="fas fa-bluetooth"></i> Print BT';
            bluetoothBtn.onclick = () => {
                if (bluetoothPrinter && bluetoothPrinter.isConnected()) {
                    bluetoothPrinter.printReceipt(orderId);
                } else {
                    alert('Bluetooth printer not connected');
                }
            };
            
            buttonContainer.appendChild(bluetoothBtn);
        }
    });
}

// Auto-add Bluetooth buttons when orders are refreshed
if (typeof refreshOrders === 'function') {
    const originalRefreshOrders = refreshOrders;
    refreshOrders = function() {
        originalRefreshOrders.call(this);
        setTimeout(addBluetoothPrintButtons, 500);
    };
}

// Add Bluetooth print option to accept order process
function setupBluetoothPrintOption() {
    const acceptButtons = document.querySelectorAll('button[onclick^="acceptOrder"]');
    
    acceptButtons.forEach(btn => {
        btn.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            
            if (bluetoothPrinter && bluetoothPrinter.isConnected()) {
                const orderId = this.onclick.toString().match(/acceptOrder\((\d+)\)/)[1];
                if (confirm('Print with Bluetooth printer?')) {
                    enhancedAcceptOrder(orderId, true);
                }
            } else {
                alert('Bluetooth printer not connected');
            }
        });
    });
}