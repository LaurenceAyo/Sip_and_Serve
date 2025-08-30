{{-- resources/views/kiosk/payment-success.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-success">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center text-white">
            <i class="fas fa-check-circle fa-5x mb-4"></i>
            <h1 class="display-4 mb-3">Payment Successful!</h1>
            <p class="lead mb-4">{{ $message ?? 'Your payment has been processed successfully.' }}</p>
            
            @if(isset($payment_intent_id))
                <p class="mb-4">
                    <small>Payment ID: {{ $payment_intent_id }}</small>
                </p>
            @endif
            
            <div class="mt-4">
                <button class="btn btn-light btn-lg" onclick="window.close()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-close after 5 seconds
        setTimeout(() => {
            window.close();
        }, 5000);
        
        // Try to communicate with parent window if opened as popup
        if (window.opener) {
            window.opener.postMessage({
                type: 'payment_success',
                payment_intent_id: '{{ $payment_intent_id ?? '' }}'
            }, '*');
        }
    </script>
</body>
</html>