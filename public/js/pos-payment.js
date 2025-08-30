// public/js/pos-payment.js

class POSPayment {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.pollInterval = null;
    }

    /**
     * Process payment for the current order
     */
    async processPayment(orderData) {
        try {
            // Show loading state
            this.showLoadingState();

            const response = await fetch('/api/pos/payment/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({
                    amount: orderData.total,
                    order_id: orderData.id,
                    customer_name: orderData.customer_name || 'Walk-in Customer',
                    payment_method: 'gcash'
                })
            });

            const result = await response.json();

            if (result.success) {
                this.handlePaymentSuccess(result.data);
            } else {
                this.handlePaymentError(result.message);
            }

        } catch (error) {
            console.error('Payment processing error:', error);
            this.handlePaymentError('Network error occurred. Please try again.');
        }
    }

    /**
     * Handle successful payment initiation
     */
    handlePaymentSuccess(data) {
        // Store payment intent ID for status checking
        sessionStorage.setItem('currentPaymentIntentId', data.payment_intent_id);

        if (data.redirect_url) {
            // Show QR code or redirect to GCash
            this.showGCashPayment(data.redirect_url, data.payment_intent_id);
        } else {
            this.handlePaymentError('No payment URL received');
        }
    }

    /**
     * Show GCash payment interface
     */
    showGCashPayment(redirectUrl, paymentIntentId) {
        // Hide loading state
        this.hideLoadingState();

        // Show GCash payment modal
        const modal = document.getElementById('gcash-payment-modal');
        const qrContainer = document.getElementById('gcash-qr-container');
        
        if (modal && qrContainer) {
            // Generate QR code for the redirect URL
            this.generateQRCode(qrContainer, redirectUrl);
            
            // Show modal
            modal.style.display = 'block';
            
            // Start polling for payment status
            this.startPaymentStatusPolling(paymentIntentId);
        } else {
            // Fallback: redirect directly
            window.open(redirectUrl, '_blank');
            this.startPaymentStatusPolling(paymentIntentId);
        }
    }

    /**
     * Generate QR code for GCash payment
     */
    generateQRCode(container, url) {
        // Clear existing QR code
        container.innerHTML = '';
        
        // You can use a QR code library like qrcode.js
        // For now, we'll create a simple link
        const link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.className = 'btn btn-primary btn-lg';
        link.innerHTML = `
            <i class="fas fa-mobile-alt"></i>
            Pay with GCash
        `;
        
        container.appendChild(link);

        // Add instruction text
        const instruction = document.createElement('p');
        instruction.className = 'text-center mt-3';
        instruction.innerHTML = 'Click the button above to pay with GCash, or scan this QR code with your phone.';
        container.appendChild(instruction);
    }

    /**
     * Start polling for payment status
     */
    startPaymentStatusPolling(paymentIntentId) {
        this.pollInterval = setInterval(async () => {
            await this.checkPaymentStatus(paymentIntentId);
        }, 3000); // Poll every 3 seconds
    }

    /**
     * Check payment status
     */
    async checkPaymentStatus(paymentIntentId) {
        try {
            const response = await fetch(`/api/pos/payment/status/${paymentIntentId}`);
            const result = await response.json();

            if (result.success) {
                const status = result.status;

                switch (status) {
                    case 'succeeded':
                        this.onPaymentCompleted(paymentIntentId);
                        break;
                    case 'failed':
                    case 'cancelled':
                        this.onPaymentFailed(status);
                        break;
                    case 'processing':
                        this.updatePaymentStatus('Processing payment...');
                        break;
                }
            }
        } catch (error) {
            console.error('Status check error:', error);
        }
    }

    /**
     * Handle completed payment
     */
    onPaymentCompleted(paymentIntentId) {
        // Stop polling
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }

        // Hide payment modal
        this.hidePaymentModal();

        // Show success message
        this.showSuccessMessage('Payment completed successfully!');

        // Clear session storage
        sessionStorage.removeItem('currentPaymentIntentId');

        // Trigger order completion
        this.completeOrder(paymentIntentId);
    }

    /**
     * Handle failed payment
     */
    onPaymentFailed(status) {
        // Stop polling
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }

        // Hide payment modal
        this.hidePaymentModal();

        // Show error message
        this.showErrorMessage(`Payment ${status}. Please try again.`);

        // Clear session storage
        sessionStorage.removeItem('currentPaymentIntentId');
    }

    /**
     * Complete the order after successful payment
     */
    completeOrder(paymentIntentId) {
        // Implement your order completion logic here
        console.log('Order completed with payment intent:', paymentIntentId);
        
        // Example: redirect to receipt page or update UI
        // window.location.href = `/pos/receipt/${paymentIntentId}`;
    }

    /**
     * UI Helper Methods
     */
    showLoadingState() {
        const button = document.getElementById('pay-button');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
    }

    hideLoadingState() {
        const button = document.getElementById('pay-button');
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-mobile-alt"></i> Pay with GCash';
        }
    }

    hidePaymentModal() {
        const modal = document.getElementById('gcash-payment-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    updatePaymentStatus(message) {
        const statusElement = document.getElementById('payment-status');
        if (statusElement) {
            statusElement.textContent = message;
        }
    }

    showSuccessMessage(message) {
        alert(message); // Replace with your preferred notification system
    }

    showErrorMessage(message) {
        alert(message); // Replace with your preferred notification system
    }

    handlePaymentError(message) {
        this.hideLoadingState();
        this.showErrorMessage(message);
    }
}

// Initialize payment handler
const posPayment = new POSPayment();

// Example usage:
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    
    if (payButton) {
        payButton.addEventListener('click', function() {
            // Get order data from your POS system
            const orderData = {
                id: 'ORDER-' + Date.now(),
                total: parseFloat(document.getElementById('order-total')?.textContent || 0),
                customer_name: document.getElementById('customer-name')?.value || null
            };

            if (orderData.total > 0) {
                posPayment.processPayment(orderData);
            } else {
                alert('Please add items to the order first.');
            }
        });
    }
});