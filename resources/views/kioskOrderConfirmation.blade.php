<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>L'PRIMERO CAFE - Review Order</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f1e8;
            min-height: 100vh;
        }

        .kiosk-container {
            height: 100vh;
            display: flex;
            background: #f5f1e8;
            width: 100%;
            overflow: hidden;
        }

        /* PAYMENT METHOD MODAL */
        .payment-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .payment-modal-overlay.show {
            display: flex;
        }

        .payment-modal-container {
            background: #ffffff;
            width: 500px;
            max-width: 85vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(44, 24, 16, 0.3);
            overflow: hidden;
            animation: slideIn 0.4s ease-out;
            border: 3px solid #d4c4a8;
        }

        .payment-modal-header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .payment-modal-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .payment-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .payment-modal-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.4;
        }

        .payment-modal-content {
            padding: 30px 25px;
            background: #f5f1e8;
        }

        .payment-amount-display {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .payment-amount-label {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .payment-amount-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #8b4513;
            font-family: 'Playfair Display', serif;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-method-btn {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 20px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .payment-method-btn:hover {
            border-color: #8b4513;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.2);
        }

        .payment-method-btn.selected {
            border-color: #8b4513;
            background: linear-gradient(135deg, #f5f1e8, #F5E6D3);
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        .payment-method-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }

        .payment-method-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 8px;
        }

        .payment-method-subtitle {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.3;
        }

        .gcash-method {
            background: linear-gradient(135deg, #007dfe, #0056b3);
            color: white;
            border-color: #007dfe;
        }

        .gcash-method:hover {
            border-color: #0056b3;
            background: linear-gradient(135deg, #0056b3, #004494);
        }

        .gcash-method .payment-method-title,
        .gcash-method .payment-method-subtitle {
            color: white;
        }

        .cash-input-section {
            display: none;
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .cash-input-section.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cash-input-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 15px;
            text-align: center;
        }

        .cash-input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .cash-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid #d4c4a8;
            border-radius: 10px;
            text-align: center;
            background: #f8f9fa;
            color: #2c1810;
        }

        .cash-input:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .currency-symbol {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            font-weight: 700;
            color: #8b4513;
        }

        .cash-change-display {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .change-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: #28a745;
            margin: 0;
        }

        .insufficient-amount {
            color: #dc3545;
        }

        .cash-instructions {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            color: #856404;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .payment-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .payment-modal-btn {
            flex: 1;
            max-width: 200px;
            padding: 18px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .payment-modal-btn-cancel {
            background: #6c757d;
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .payment-modal-btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .payment-modal-btn-proceed {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .payment-modal-btn-proceed:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .payment-modal-btn:disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .payment-modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .payment-modal-btn:hover::before {
            left: 100%;
        }

        /* GCash Processing Modal */
        .gcash-processing-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3000;
            backdrop-filter: blur(10px);
        }

        .gcash-processing-modal.show {
            display: flex;
        }

        .gcash-processing-container {
            background: linear-gradient(135deg, #007dfe, #0056b3);
            width: 600px;
            max-width: 90vw;
            border-radius: 25px;
            box-shadow: 0 30px 60px rgba(0, 125, 254, 0.4);
            overflow: hidden;
            color: white;
            animation: slideInScale 0.5s ease-out;
        }

        @keyframes slideInScale {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .gcash-processing-header {
            background: linear-gradient(135deg, #0056b3, #004494);
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .gcash-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: #007dfe;
            font-weight: bold;
        }

        .gcash-processing-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .gcash-processing-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .gcash-processing-content {
            padding: 40px 30px;
            text-align: center;
        }

        .processing-step {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .processing-step-number {
            background: white;
            color: #007dfe;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0 auto 15px;
        }

        .processing-step-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .processing-step-description {
            font-size: 1rem;
            opacity: 0.9;
            line-height: 1.4;
        }

        .payment-details-box {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 1.1rem;
        }

        .payment-detail-row.total {
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .redirect-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #ffd700;
        }

        .redirect-info-icon {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .redirect-info-text {
            font-size: 1rem;
            line-height: 1.4;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .gcash-processing-actions {
            padding: 0 30px 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .gcash-btn {
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .gcash-btn-primary {
            background: white;
            color: #007dfe;
            flex: 1;
            max-width: 200px;
        }

        .gcash-btn-primary:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .gcash-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            flex: 1;
            max-width: 200px;
        }

        .gcash-btn-secondary:hover {
            background: white;
            color: #007dfe;
        }

        /* Success/Error States */
        .status-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 4000;
            backdrop-filter: blur(5px);
        }

        .status-modal.show {
            display: flex;
        }

        .status-container {
            background: white;
            width: 500px;
            max-width: 90vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
        }

        .status-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 40px;
        }

        .status-error {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 40px;
        }

        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .status-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .status-message {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .status-content {
            padding: 30px;
        }

        .status-actions {
            padding: 20px 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        /* CANCEL ORDER MODAL */
        .cancel-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .cancel-modal-overlay.show {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .cancel-modal-container {
            background: #ffffff;
            width: 500px;
            max-width: 90vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(44, 24, 16, 0.3);
            overflow: hidden;
            animation: slideIn 0.4s ease-out;
            border: 3px solid #d4c4a8;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .cancel-modal-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .cancel-modal-icon {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .cancel-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .cancel-modal-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.4;
        }

        .cancel-modal-content {
            padding: 40px 30px;
            text-align: center;
            background: #f5f1e8;
        }

        .cancel-warning-box {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .cancel-warning-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #856404;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cancel-warning-text {
            color: #856404;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .cancel-confirmation-text {
            font-size: 1.2rem;
            color: #2c1810;
            font-weight: 600;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .cancel-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .cancel-modal-btn {
            flex: 1;
            max-width: 180px;
            padding: 18px 25px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .cancel-modal-btn-no {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        .cancel-modal-btn-no:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .cancel-modal-btn-yes {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .cancel-modal-btn-yes:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .cancel-modal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .cancel-modal-btn:hover::before {
            left: 100%;
        }

        /* Left Sidebar */
        .sidebar {
            flex: 0 0 200px;
            min-width: 200px;
            max-width: 200px;
            background: #F5E6D3;
            border-right: 5px solid #d4c4a8;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-header {
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #d4c4a8;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c1810;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f5f1e8;
        }

        .review-header {
            padding: 40px;
            text-align: center;
            background: #F5E6D3;
            border-bottom: 3px solid #d4c4a8;
        }

        .review-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 2px;
            margin: 0;
        }

        /* Order Items Section */
        .order-items-section {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        .order-item {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            border-color: #d4c4a8;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .order-item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 20px;
            background: #f0f0f0;
            border: 2px solid #e9ecef;
        }

        .order-item-details {
            flex: 1;
            min-width: 0;
        }

        .order-item-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .order-item-addons {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.3;
            font-style: italic;
        }

        .order-item-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px 12px;
            border: 2px solid #e9ecef;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: #8b4513;
            color: white;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #6d3410;
            transform: scale(1.1);
        }

        .quantity-display {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c1810;
            min-width: 30px;
            text-align: center;
        }

        .order-item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #8b4513;
            margin-left: 20px;
        }

        .order-item-remove {
            width: 40px;
            height: 40px;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 20px;
            transition: all 0.3s ease;
        }

        .order-item-remove:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        /* Bottom Summary Section */
        .order-summary {
            background: white;
            border-top: 3px solid #d4c4a8;
            padding: 30px 40px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 1.1rem;
            color: #2c1810;
        }

        .summary-row.total {
            border-top: 2px solid #d4c4a8;
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #8b4513;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 18px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-pay {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .btn-pay::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-pay:hover::before {
            left: 100%;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #8b4513;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .empty-cart p {
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: 150px !important;
                min-width: 150px;
                max-width: 150px;
            }

            .review-title {
                font-size: 2.2rem;
            }

            .order-items-section {
                padding: 20px;
            }

            .order-summary {
                padding: 20px;
            }

            .order-item {
                padding: 15px;
                flex-direction: column;
                text-align: center;
            }

            .order-item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .order-item-controls {
                justify-content: center;
                margin-top: 15px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .cancel-modal-container,
            .payment-modal-container,
            .gcash-processing-container {
                width: 400px;
            }

            .cancel-modal-actions,
            .payment-modal-actions,
            .gcash-processing-actions {
                flex-direction: column;
                gap: 12px;
            }

            .cancel-modal-btn,
            .payment-modal-btn,
            .gcash-btn {
                max-width: none;
            }

            .payment-methods {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        /* Lenovo Xiaoxin Pad 2024 11" Optimizations */
        @media (min-width: 1200px) and (max-width: 1920px) {
            .review-title {
                font-size: 3.5rem;
            }

            .order-item {
                padding: 25px;
            }

            .order-item-image {
                width: 90px;
                height: 90px;
            }

            .order-item-name {
                font-size: 1.3rem;
            }

            .order-item-price {
                font-size: 1.3rem;
            }

            .quantity-btn {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .btn {
                padding: 22px 35px;
                font-size: 1.2rem;
            }

            .summary-row {
                font-size: 1.2rem;
            }

            .summary-row.total {
                font-size: 1.5rem;
            }

            .cancel-modal-container,
            .payment-modal-container,
            .gcash-processing-container {
                width: 600px;
            }

            .cancel-modal-title,
            .payment-modal-title {
                font-size: 2rem;
            }

            .cancel-confirmation-text {
                font-size: 1.3rem;
            }

            .cancel-modal-btn,
            .payment-modal-btn {
                padding: 20px 30px;
                font-size: 1.1rem;
            }
        }

        /* Portrait mode optimization for tablet */
        @media (orientation: portrait) and (min-width: 768px) {
            .sidebar {
                width: 180px !important;
                min-width: 180px;
                max-width: 180px;
            }
        }
    </style>
</head>

<body>
    <div class="kiosk-container">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Sip & Serve</h2>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Review Header -->
            <header class="review-header">
                <h1 class="review-title">Review Order</h1>
            </header>

            <!-- Order Items Section -->
            <section class="order-items-section" id="orderItemsSection">
                @if(session('cart') && count(session('cart')) > 0)
                    @foreach(session('cart') as $index => $item)
                        <div class="order-item">
                            <img src="{{ $item['image'] ?? 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 80 80\'><rect width=\'80\' height=\'80\' fill=\'%23F5F5F5\'/><circle cx=\'40\' cy=\'40\' r=\'20\' fill=\'%238B4513\'/></svg>' }}"
                                alt="{{ $item['name'] }}" class="order-item-image">

                            <div class="order-item-details">
                                <div class="order-item-name">{{ $item['name'] }}</div>
                                @if(isset($item['addons']) && count($item['addons']) > 0)
                                    <div class="order-item-addons">
                                        Add-ons: {{ implode(', ', array_column($item['addons'], 'name')) }}
                                    </div>
                                @endif

                                <div class="order-item-controls">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateItemQuantity({{ $index }}, -1)">−</button>
                                        <span class="quantity-display">{{ $item['quantity'] }}</span>
                                        <button class="quantity-btn" onclick="updateItemQuantity({{ $index }}, 1)">+</button>
                                    </div>
                                    <div class="order-item-price">
                                        PHP
                                        {{ number_format(($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'], 2) }}
                                    </div>
                                </div>
                            </div>

                            <button class="order-item-remove" onclick="removeItem({{ $index }})">
                                🗑️
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="empty-cart">
                        <h3>Your cart is empty</h3>
                        <p>Please add items to your cart before reviewing your order.</p>
                        <a href="{{ route('kiosk.main', ['orderType' => session('orderType', 'dine-in')]) }}"
                            class="btn btn-back">
                            Back to Menu
                        </a>
                    </div>
                @endif
            </section>

            @if(session('cart') && count(session('cart')) > 0)
                <!-- Order Summary -->
                <footer class="order-summary">
                    @php
                        $cart = session('cart', []);
                        $subtotal = 0;
                        foreach ($cart as $item) {
                            $subtotal += ($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'];
                        }
                        $discounts = 0;
                        $total = $subtotal - $discounts;
                    @endphp

                    <div class="summary-row">
                        <span><strong>Sub Total:</strong></span>
                        <span id="subtotalAmount">PHP {{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if($discounts > 0)
                        <div class="summary-row">
                            <span><strong>Discounts:</strong></span>
                            <span id="discountAmount">PHP {{ number_format($discounts, 2) }}</span>
                        </div>
                    @endif

                    <div class="summary-row total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="totalAmount">PHP {{ number_format($total, 2) }}</span>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('kiosk.main', ['orderType' => session('orderType', 'dine-in')]) }}"
                            class="btn btn-back">
                            Back to Menu
                        </a>
                        <button type="button" class="btn btn-cancel" onclick="showCancelModal()">
                            Cancel Order
                        </button>
                        <button type="button" class="btn btn-pay" onclick="showPaymentModal()">
                            PAY
                        </button>
                    </div>
                </footer>
            @endif
        </main>
    </div>

    <!-- Payment Method Modal -->
    <div class="payment-modal-overlay" id="paymentModal">
        <div class="payment-modal-container">
            <div class="payment-modal-header">
                <div class="payment-modal-icon">💳</div>
                <h2 class="payment-modal-title">Choose Payment Method</h2>
                <p class="payment-modal-subtitle">Select how you'd like to pay for your order</p>
            </div>

            <div class="payment-modal-content">
                <div class="payment-amount-display">
                    <div class="payment-amount-label">Total Amount to Pay:</div>
                    <div class="payment-amount-value" id="paymentTotalAmount">
                        PHP {{ number_format($total ?? 0, 2) }}
                    </div>
                </div>

                <div class="payment-methods">
                    <div class="payment-method-btn gcash-method" onclick="selectPaymentMethod('gcash')">
                        <span class="payment-method-icon">📱</span>
                        <div class="payment-method-title">GCash</div>
                        <div class="payment-method-subtitle">E-Wallet Payment</div>
                    </div>

                    <div class="payment-method-btn" onclick="selectPaymentMethod('cash')">
                        <span class="payment-method-icon">💵</span>
                        <div class="payment-method-title">Cash</div>
                        <div class="payment-method-subtitle">Pay with Cash</div>
                    </div>
                </div>

                <!-- Cash Input Section -->
                <div class="cash-input-section" id="cashInputSection">
                    <div class="cash-input-label">Enter amount to be paid:</div>
                    <div class="cash-input-wrapper">
                        <span class="currency-symbol">₱</span>
                        <input type="number" class="cash-input" id="cashAmountInput" placeholder="0.00" step="0.01"
                            min="0" onkeyup="calculateChange()" onchange="calculateChange()">
                    </div>

                    <div class="cash-change-display" id="changeDisplay" style="display: none;">
                        <div class="change-amount" id="changeAmount"></div>
                    </div>

                    <div class="cash-instructions">
                        💡 <strong>Instructions:</strong> Please enter the exact amount you'll pay with cash. After
                        confirming, proceed to the cashier to complete your order and receive your change.
                    </div>
                </div>

                <div class="payment-modal-actions">
                    <button class="payment-modal-btn payment-modal-btn-cancel" onclick="hidePaymentModal()">
                        Cancel
                    </button>
                    <button class="payment-modal-btn payment-modal-btn-proceed" id="proceedPaymentBtn"
                        onclick="proceedWithPayment()" disabled>
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- GCash Processing Modal -->
    <div class="gcash-processing-modal" id="gcashProcessingModal">
        <div class="gcash-processing-container">
            <div class="gcash-processing-header">
                <div class="gcash-logo">G₱</div>
                <h2 class="gcash-processing-title">Preparing GCash Payment</h2>
                <p class="gcash-processing-subtitle">Setting up your secure payment...</p>
            </div>
            
            <div class="gcash-processing-content">
                <div class="processing-step">
                    <div class="processing-step-number" id="stepNumber">1</div>
                    <div class="processing-step-title" id="stepTitle">Creating Payment Intent</div>
                    <div class="processing-step-description" id="stepDescription">
                        Securely connecting to PayMongo payment gateway
                    </div>
                </div>

                <div class="payment-details-box">
                    <div class="payment-detail-row">
                        <span>Order Amount:</span>
                        <span id="gcashOrderAmount">PHP {{ number_format($total ?? 0, 2) }}</span>
                    </div>
                    <div class="payment-detail-row">
                        <span>Payment Method:</span>
                        <span>GCash via PayMongo</span>
                    </div>
                    <div class="payment-detail-row total">
                        <span>Total to Pay:</span>
                        <span id="gcashTotalAmount">PHP {{ number_format($total ?? 0, 2) }}</span>
                    </div>
                </div>

                <div class="redirect-info">
                    <div class="redirect-info-icon">🔒</div>
                    <div class="redirect-info-text">
                        <strong>What happens next?</strong><br>
                        You'll be redirected to PayMongo's secure GCash checkout page. Complete your payment there and you'll be automatically returned to confirm your order.
                    </div>
                </div>

                <div class="spinner" id="loadingSpinner"></div>
            </div>

            <div class="gcash-processing-actions">
                <button class="gcash-btn gcash-btn-primary" id="continueToGCashBtn" onclick="redirectToGCash()" style="display: none;">
                    Continue to GCash
                </button>
                <button class="gcash-btn gcash-btn-secondary" onclick="cancelGCashPayment()">
                    Cancel Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div class="status-modal" id="successModal">
        <div class="status-container">
            <div class="status-success">
                <div class="status-icon">✅</div>
                <h2 class="status-title">Payment Successful!</h2>
                <p class="status-message">Your GCash payment has been processed successfully</p>
            </div>
            <div class="status-content">
                <p>Your order has been confirmed and sent to the kitchen.</p>
                <p>Please wait for your order to be prepared.</p>
            </div>
            <div class="status-actions">
                <button class="payment-modal-btn payment-modal-btn-proceed" onclick="completeOrder()">
                    Complete Order
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Error Modal -->
    <div class="status-modal" id="errorModal">
        <div class="status-container">
            <div class="status-error">
                <div class="status-icon">❌</div>
                <h2 class="status-title">Payment Failed</h2>
                <p class="status-message" id="errorMessage">There was an issue processing your GCash payment</p>
            </div>
            <div class="status-content">
                <p>Your order has not been placed. Please try again or choose a different payment method.</p>
            </div>
            <div class="status-actions">
                <button class="payment-modal-btn payment-modal-btn-cancel" onclick="closeErrorModal()">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="cancel-modal-overlay" id="cancelModal">
        <div class="cancel-modal-container">
            <div class="cancel-modal-header">
                <div class="cancel-modal-icon">⚠️</div>
                <h2 class="cancel-modal-title">Cancel Order?</h2>
                <p class="cancel-modal-subtitle">This action cannot be undone</p>
            </div>

            <div class="cancel-modal-content">
                <div class="cancel-warning-box">
                    <div class="cancel-warning-title">
                        <span>📋</span>
                        What will happen:
                    </div>
                    <div class="cancel-warning-text">
                        • All items will be removed from your cart<br>
                        • Your order will be completely deleted<br>
                        • You'll return to the main kiosk screen
                    </div>
                </div>

                <div class="cancel-confirmation-text">
                    Are you sure you want to cancel your entire order?
                </div>

                <div class="cancel-modal-actions">
                    <button class="cancel-modal-btn cancel-modal-btn-no" onclick="hideCancelModal()">
                        No, Keep Order
                    </button>
                    <button class="cancel-modal-btn cancel-modal-btn-yes" onclick="confirmCancelOrder()">
                        Yes, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Payment Confirmation Screen -->
    <div class="payment-modal-overlay" id="cashConfirmationModal">
        <div class="payment-modal-container" style="max-width: 700px;">
            <div class="payment-modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <div class="payment-modal-icon">✅</div>
                <h2 class="payment-modal-title">Order Placed Successfully!</h2>
                <p class="payment-modal-subtitle">Your order has been prepared and is ready for payment</p>
            </div>

            <div class="payment-modal-content">
                <!-- Order Number Display -->
                <div class="payment-amount-display"
                    style="background: linear-gradient(135deg, #8b4513, #a0522d); color: white; margin-bottom: 25px;">
                    <div class="payment-amount-label" style="color: rgba(255,255,255,0.9);">Your Order Number</div>
                    <div class="payment-amount-value" id="orderNumberDisplay"
                        style="color: white; font-size: 3rem; margin: 10px 0;">
                        C001
                    </div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Show this to the cashier</div>
                </div>

                <!-- Cashier Instructions -->
                <div
                    style="background: #fff3cd; border: 3px solid #ffeaa7; border-radius: 15px; padding: 25px; margin: 25px 0; text-align: center; color: #856404;">
                    <div
                        style="font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; font-family: 'Inter', sans-serif;">
                        🗣️ <strong>Please tell the cashier:</strong>
                    </div>
                    <div style="font-size: 1.1rem; background: white; padding: 15px; border-radius: 10px; font-weight: 700; color: #2c1810; font-family: 'Inter', sans-serif;"
                        id="cashierInstruction">
                        "Order #C001 - Cash Payment"
                    </div>
                </div>

                <!-- Payment Details -->
                <div
                    style="background: #f8f9fa; border-radius: 15px; padding: 25px; margin: 25px 0; border: 2px solid #e9ecef;">
                    <div
                        style="display: flex; justify-content: space-between; padding: 12px 0; font-size: 1.1rem; color: #2c1810; font-family: 'Inter', sans-serif;">
                        <span style="font-weight: 500;">Order Total:</span>
                        <span
                            style="font-weight: 700; font-family: 'Inter', sans-serif; color: #8b4513; font-size: 1.2rem;"
                            id="confirmationTotal">PHP 0.00</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; padding: 12px 0; font-size: 1.1rem; color: #2c1810; font-family: 'Inter', sans-serif;">
                        <span style="font-weight: 500;">You're Paying:</span>
                        <span
                            style="font-weight: 700; font-family: 'Inter', sans-serif; color: #8b4513; font-size: 1.2rem;"
                            id="confirmationCash">PHP 0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 15px; margin-top: 15px; background: #d4edda; border: 2px solid #c3e6cb; border-radius: 10px; color: #155724; font-weight: 700; font-size: 1.2rem; font-family: 'Inter', sans-serif;"
                        id="confirmationChangeRow">
                        <span style="font-weight: 600;">💰 Your Change:</span>
                        <span style="font-weight: 800; font-family: 'Inter', sans-serif;" id="confirmationChange">PHP
                            0.00</span>
                    </div>
                </div>

                <!-- Step-by-step Instructions -->
                <div
                    style="background: #e7f3ff; border: 2px solid #b3d9ff; border-radius: 15px; padding: 20px; margin: 25px 0; text-align: left;">
                    <div
                        style="display: flex; align-items: flex-start; margin-bottom: 15px; font-size: 1rem; line-height: 1.4;">
                        <div
                            style="background: #8b4513; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; font-size: 0.9rem;">
                            1</div>
                        <div style="font-family: 'Inter', sans-serif; color: #2c1810;">Walk to the cashier counter with
                            your order number</div>
                    </div>
                    <div
                        style="display: flex; align-items: flex-start; margin-bottom: 15px; font-size: 1rem; line-height: 1.4;">
                        <div
                            style="background: #8b4513; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; font-size: 0.9rem;">
                            2</div>
                        <div style="font-family: 'Inter', sans-serif; color: #2c1810;" id="step2Text">Tell them: "Order
                            #C001 - Cash Payment"</div>
                    </div>
                    <div
                        style="display: flex; align-items: flex-start; margin-bottom: 15px; font-size: 1rem; line-height: 1.4;">
                        <div
                            style="background: #8b4513; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; font-size: 0.9rem;">
                            3</div>
                        <div style="font-family: 'Inter', sans-serif; color: #2c1810;" id="step3Text">Pay PHP 0.00 and
                            collect your change</div>
                    </div>
                    <div
                        style="display: flex; align-items: flex-start; margin-bottom: 15px; font-size: 1rem; line-height: 1.4;">
                        <div
                            style="background: #8b4513; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0; font-size: 0.9rem;">
                            4</div>
                        <div style="font-family: 'Inter', sans-serif; color: #2c1810;">Wait for your order to be
                            prepared and served</div>
                    </div>
                </div>

                <!-- Table Information (if available) -->
                <div style="background: #f5f1e8; border-radius: 10px; padding: 15px; margin: 20px 0; border: 2px solid #d4c4a8; font-weight: 600; color: #2c1810; text-align: center; display: none;"
                    id="tableInfoSection">
                    📍 <strong id="tableInfo">Table 1</strong>
                </div>

                <div class="payment-modal-actions" style="margin-top: 30px;">
                    <button class="payment-modal-btn"
                        style="background: linear-gradient(135deg, #28a745, #20c997); color: white;"
                        onclick="completeOrder()">
                        Complete Order
                    </button>
                    <button class="payment-modal-btn payment-modal-btn-cancel" onclick="orderMore()">
                        Order More Items
                    </button>
                </div>

                <!-- Footer note -->
                <div style="margin-top: 30px; font-size: 0.9rem; color: #666; font-style: italic; text-align: center;">
                    Thank you for choosing Sip & Serve! 🍽️
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = @json(session('cart', []));
        let selectedPaymentMethod = null;
        let totalAmount = {{ $total ?? 0 }};
        let checkoutUrl = null;
        let orderData = null;

        function updateItemQuantity(index, change) {
            fetch('{{ route("kiosk.updateCartItem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    index: index,
                    change: change
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating item quantity');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating item quantity');
                });
        }

        function removeItem(index) {
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('{{ route("kiosk.removeCartItem") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        index: index
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error removing item');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error removing item');
                    });
            }
        }

        // PAYMENT MODAL FUNCTIONS
        function showPaymentModal() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const modal = document.getElementById('paymentModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Reset payment method selection
            selectedPaymentMethod = null;
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            document.getElementById('cashInputSection').classList.remove('show');
            document.getElementById('proceedPaymentBtn').disabled = true;
            document.getElementById('proceedPaymentBtn').textContent = 'Select Payment Method';
        }

        function hidePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';

            // Reset form
            selectedPaymentMethod = null;
            document.getElementById('cashAmountInput').value = '';
            document.getElementById('changeDisplay').style.display = 'none';
            document.getElementById('cashInputSection').classList.remove('show');
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;

            // Update UI
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('selected');
            });

            const selectedBtn = event.currentTarget;
            selectedBtn.classList.add('selected');

            const proceedBtn = document.getElementById('proceedPaymentBtn');
            const cashSection = document.getElementById('cashInputSection');

            if (method === 'gcash') {
                cashSection.classList.remove('show');
                proceedBtn.disabled = false;
                proceedBtn.textContent = 'Pay with GCash';
            } else if (method === 'cash') {
                cashSection.classList.add('show');
                proceedBtn.disabled = true;
                proceedBtn.textContent = 'Enter Cash Amount';
                document.getElementById('cashAmountInput').focus();
            }
        }

        function calculateChange() {
            const cashInput = document.getElementById('cashAmountInput');
            const changeDisplay = document.getElementById('changeDisplay');
            const changeAmount = document.getElementById('changeAmount');
            const proceedBtn = document.getElementById('proceedPaymentBtn');

            const cashValue = parseFloat(cashInput.value) || 0;
            const change = cashValue - totalAmount;

            if (cashValue > 0) {
                changeDisplay.style.display = 'block';

                if (change >= 0) {
                    changeAmount.textContent = `Change: PHP ${change.toFixed(2)}`;
                    changeAmount.className = 'change-amount';
                    proceedBtn.disabled = false;
                    proceedBtn.textContent = 'Proceed to Cashier';
                } else {
                    changeAmount.textContent = `Insufficient amount: PHP ${Math.abs(change).toFixed(2)} short`;
                    changeAmount.className = 'change-amount insufficient-amount';
                    proceedBtn.disabled = true;
                    proceedBtn.textContent = 'Insufficient Amount';
                }
            } else {
                changeDisplay.style.display = 'none';
                proceedBtn.disabled = true;
                proceedBtn.textContent = 'Enter Cash Amount';
            }
        }

        function proceedWithPayment() {
            if (!selectedPaymentMethod) {
                alert('Please select a payment method');
                return;
            }

            if (selectedPaymentMethod === 'gcash') {
                // Process GCash payment via PayMongo
                processGCashPayment();
            } else if (selectedPaymentMethod === 'cash') {
                // Process cash payment
                processCashPayment();
            }
        }

        function processGCashPayment() {
            // Hide payment modal and show GCash processing
            hidePaymentModal();
            document.getElementById('gcashProcessingModal').classList.add('show');

            orderData = {
                order_type: '{{ session("orderType", "dine-in") }}',
                items: cart,
                subtotal: totalAmount,
                tax_amount: 0,
                discount_amount: 0,
                total_amount: totalAmount,
                payment_method: 'gcash'
            };

            fetch('{{ route("kiosk.processPayment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.checkout_url) {
                        checkoutUrl = data.checkout_url;
                        
                        // Store order info for return
                        sessionStorage.setItem('pendingGCashOrder', JSON.stringify(data));

                        // Hide spinner and show continue button
                        document.getElementById('loadingSpinner').style.display = 'none';
                        document.getElementById('continueToGCashBtn').style.display = 'block';
                        
                        // Update step indicator
                        updateProcessingStep('Payment intent created successfully', '2');
                    } else {
                        handleGCashError(data.message || 'Payment processing failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    handleGCashError('Network error occurred');
                });
        }

        function updateProcessingStep(message, stepNumber) {
            document.getElementById('stepNumber').textContent = stepNumber;
            document.getElementById('stepTitle').textContent = 'Ready for Payment';
            document.getElementById('stepDescription').textContent = message;
        }

        function redirectToGCash() {
            if (checkoutUrl) {
                // Store order info for when user returns
                sessionStorage.setItem('pendingOrder', JSON.stringify(orderData));
                
                // Redirect to PayMongo GCash checkout
                window.location.href = checkoutUrl;
            } else {
                handleGCashError('No checkout URL available');
            }
        }

        function cancelGCashPayment() {
            document.getElementById('gcashProcessingModal').classList.remove('show');
            document.getElementById('paymentModal').classList.add('show');
        }

        function handleGCashError(message) {
            document.getElementById('gcashProcessingModal').classList.remove('show');
            document.getElementById('errorModal').classList.add('show');
            
            // Update error message
            document.getElementById('errorMessage').textContent = message;
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.remove('show');
            document.getElementById('paymentModal').classList.add('show');
        }

        function showPaymentSuccess() {
            document.getElementById('gcashProcessingModal').classList.remove('show');
            document.getElementById('successModal').classList.add('show');
        }

        function processCashPayment() {
            const cashAmount = parseFloat(document.getElementById('cashAmountInput').value);

            if (cashAmount < totalAmount) {
                alert('Insufficient cash amount');
                return;
            }

            // Show loading state
            const proceedBtn = document.getElementById('proceedPaymentBtn');
            proceedBtn.disabled = true;
            proceedBtn.textContent = 'Processing...';

            const orderData = {
                order_type: '{{ session("orderType", "dine-in") }}',
                items: cart,
                subtotal: totalAmount,
                tax_amount: 0,
                discount_amount: 0,
                total_amount: totalAmount,
                payment_method: 'cash',
                cash_amount: cashAmount,
                change_amount: cashAmount - totalAmount
            };

            fetch('{{ route("kiosk.processPayment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide payment modal
                        hidePaymentModal();

                        // Show cash payment confirmation screen
                        showCashPaymentConfirmation(data);
                    } else {
                        alert('Error processing cash payment: ' + (data.message || 'Unknown error'));
                        proceedBtn.disabled = false;
                        proceedBtn.textContent = 'Proceed to Cashier';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing payment. Please try again.');
                    proceedBtn.disabled = false;
                    proceedBtn.textContent = 'Proceed to Cashier';
                });
        }

        // CANCEL ORDER MODAL FUNCTIONS
        function showCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        function confirmCancelOrder() {
            fetch('{{ route("kiosk.cancelOrder") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '{{ route("kiosk.index") }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '{{ route("kiosk.index") }}';
                });
        }

        // Close modals when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function (e) {
            if (e.target === this) {
                hideCancelModal();
            }
        });

        document.getElementById('paymentModal').addEventListener('click', function (e) {
            if (e.target === this) {
                hidePaymentModal();
            }
        });

        document.getElementById('gcashProcessingModal').addEventListener('click', function (e) {
            if (e.target === this) {
                cancelGCashPayment();
            }
        });

        // Add touch feedback for better UX
        document.querySelectorAll('.btn, .quantity-btn, .order-item-remove, .cancel-modal-btn, .payment-modal-btn, .payment-method-btn, .gcash-btn').forEach(button => {
            button.addEventListener('touchstart', function () {
                this.style.transform = 'scale(0.98)';
            });

            button.addEventListener('touchend', function () {
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Handle Enter key in cash input
        document.getElementById('cashAmountInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !document.getElementById('proceedPaymentBtn').disabled) {
                proceedWithPayment();
            }
        });

        // CASH PAYMENT CONFIRMATION FUNCTIONS
        function showCashPaymentConfirmation(data) {
            const modal = document.getElementById('cashConfirmationModal');

            // Generate order number
            const orderNumber = data.order_number || 'C' + String(data.order_id || Math.floor(Math.random() * 1000)).padStart(3, '0');

            // Update order number displays
            document.getElementById('orderNumberDisplay').textContent = orderNumber;
            document.getElementById('cashierInstruction').textContent = `"Order #${orderNumber} - Cash Payment"`;
            document.getElementById('step2Text').textContent = `Tell them: "Order #${orderNumber} - Cash Payment"`;

            // Get payment amounts from the response data
            const cashAmount = parseFloat(data.cash_amount) || parseFloat(document.getElementById('cashAmountInput').value) || 0;
            const totalAmountFromData = parseFloat(data.total_amount) || totalAmount || 0;
            const changeAmount = parseFloat(data.change_amount) || (cashAmount - totalAmountFromData);

            // Update payment amounts with proper formatting
            document.getElementById('confirmationTotal').textContent = `PHP ${totalAmountFromData.toFixed(2)}`;
            document.getElementById('confirmationCash').textContent = `PHP ${cashAmount.toFixed(2)}`;
            document.getElementById('confirmationChange').textContent = `PHP ${changeAmount.toFixed(2)}`;
            document.getElementById('step3Text').textContent = `Pay PHP ${cashAmount.toFixed(2)} and collect your change`;

            // Show/hide change row if no change needed
            if (changeAmount <= 0) {
                document.getElementById('confirmationChangeRow').style.display = 'none';
            } else {
                document.getElementById('confirmationChangeRow').style.display = 'flex';
            }

            // Hide table info section by default
            const tableInfoSection = document.getElementById('tableInfoSection');
            if (tableInfoSection) {
                tableInfoSection.style.display = 'none';
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function completeOrder() {
            // Redirect to kiosk home or confirmation page
            window.location.href = '{{ route("kiosk.index") }}';
        }

        function orderMore() {
            // Redirect back to menu
            window.location.href = '{{ route("kiosk.main") }}';
        }

        // Close cash confirmation modal when clicking outside
        document.getElementById('cashConfirmationModal').addEventListener('click', function (e) {
            if (e.target === this) {
                completeOrder();
            }
        });

        // Handle return from PayMongo
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentIntentId = urlParams.get('payment_intent_id');
            
            if (paymentIntentId) {
                // Payment completed - show success
                showPaymentSuccess();
            }
        });
    </script>
</body>

</html>