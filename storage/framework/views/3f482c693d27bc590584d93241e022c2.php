<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

        .maya-method {
            background: linear-gradient(135deg, #007dfe, #0056b3);
            color: white;
            border-color: #007dfe;
        }

        .maya-method:hover {
            border-color: #0056b3;
            background: linear-gradient(135deg, #0056b3, #004494);
        }

        .maya-method .payment-method-title,
        .maya-method .payment-method-subtitle {
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

        /* Payment Error Modal Styles */
        .payment-error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #f5c6cb;
        }

        .payment-error-suggestions {
            margin: 20px 0;
            padding: 15px;
            background: #fff3cd;
            border-radius: 8px;
            border: 1px solid #ffeaa7;
        }

        .payment-error-suggestions h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #856404;
        }

        .payment-error-suggestions ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }

        .payment-error-suggestions li {
            margin-bottom: 5px;
        }

        .payment-modal-btn:disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Cash Payment Confirmation Modal */
        .cash-confirmation-modal {
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

        .cash-confirmation-modal.show {
            display: flex;
        }

        .cash-confirmation-container {
            background: white;
            width: 700px;
            max-width: 95vw;
            border-radius: 25px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
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

        .cash-confirmation-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .cash-confirmation-content {
            padding: 40px 30px;
            background: #f5f1e8;
        }

        .order-number-display {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.3);
        }

        .order-number-label {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .order-number-value {
            font-size: 3rem;
            font-weight: 800;
            font-family: 'Playfair Display', serif;
            margin: 10px 0;
        }

        .order-number-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .cashier-instructions {
            background: #fff3cd;
            border: 3px solid #ffeaa7;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            color: #856404;
            text-align: center;
        }

        .cashier-instruction-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .cashier-instruction-text {
            background: white;
            padding: 15px;
            border-radius: 10px;
            font-weight: 700;
            color: #2c1810;
            font-size: 1.1rem;
        }

        .payment-details-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            border: 2px solid #e9ecef;
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 1.1rem;
            color: #2c1810;
        }

        .payment-detail-row.total {
            border-top: 2px solid #d4c4a8;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #8b4513;
        }

        .change-highlight {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            color: #155724;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .steps-list {
            background: #e7f3ff;
            border: 2px solid #b3d9ff;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            font-size: 1rem;
            line-height: 1.4;
        }

        .step-number {
            background: #8b4513;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 0.9rem;
        }

        .step-text {
            color: #2c1810;
        }

        .confirmation-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .confirmation-btn {
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
        }

        .confirmation-btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .confirmation-btn-primary:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            transform: translateY(-2px);
        }

        .confirmation-btn-secondary {
            background: #6c757d;
            color: white;
        }

        .confirmation-btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .footer-note {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
            text-align: center;
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
            margin-bottom: 6rem;
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
            .cash-confirmation-container {
                width: 400px;
            }

            .cancel-modal-actions,
            .payment-modal-actions,
            .confirmation-actions {
                flex-direction: column;
                gap: 12px;
            }

            .cancel-modal-btn,
            .payment-modal-btn,
            .confirmation-btn {
                max-width: none;
            }

            .payment-methods {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        /* GCash Payment Modal Styles */
        .gcash-payment-section {
            padding: 20px 0;
            text-align: center;
        }

        .gcash-qr-container {
            margin: 20px 0;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gcash-payment-button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .gcash-payment-button {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .gcash-payment-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .gcash-icon {
            font-size: 24px;
        }

        .gcash-button-note {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .gcash-status-message {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
        }

        .status-icon {
            font-size: 24px;
        }

        .status-text {
            font-weight: 500;
            color: #495057;
        }

        .gcash-instructions {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }

        .instruction-title {
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 10px;
        }

        .instruction-steps {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .step {
            padding-left: 15px;
            color: #424242;
        }

        /* Animation for modals */
        .payment-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .payment-modal-overlay.show .payment-modal-container {
            transform: translateY(0);
        }

        .modal-overlay {
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
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .modal-header h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c1810;
        }

        .modal-content {
            text-align: center;
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
                <?php if(session('cart') && count(session('cart')) > 0): ?>
                    <?php $__currentLoopData = session('cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-item">
                            <img src="<?php echo e($item['image'] ?? 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 80 80\'><rect width=\'80\' height=\'80\' fill=\'%23F5F5F5\'/><circle cx=\'40\' cy=\'40\' r=\'20\' fill=\'%238B4513\'/></svg>'); ?>"
                                alt="<?php echo e($item['name']); ?>" class="order-item-image">

                            <div class="order-item-details">
                                <div class="order-item-name"><?php echo e($item['name']); ?></div>
                                <?php if(isset($item['addons']) && count($item['addons']) > 0): ?>
                                    <div class="order-item-addons">
                                        Add-ons: <?php echo e(implode(', ', array_column($item['addons'], 'name'))); ?>

                                    </div>
                                <?php endif; ?>

                                <div class="order-item-controls">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateItemQuantity(<?php echo e($index); ?>, -1)">−</button>
                                        <span class="quantity-display"><?php echo e($item['quantity']); ?></span>
                                        <button class="quantity-btn" onclick="updateItemQuantity(<?php echo e($index); ?>, 1)">+</button>
                                    </div>
                                    <div class="order-item-price">
                                        PHP
                                        <?php echo e(number_format(($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'], 2)); ?>

                                    </div>
                                </div>
                            </div>

                            <button class="order-item-remove" onclick="removeItem(<?php echo e($index); ?>)">🗑️</button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="empty-cart">
                        <h3>Your cart is empty</h3>
                        <p>Please add items to your cart before reviewing your order.</p>
                        <a href="<?php echo e(route('kiosk.main', ['orderType' => session('orderType', 'dine-in')])); ?>"
                            class="btn btn-back">
                            Back to Menu
                        </a>
                    </div>
                <?php endif; ?>
            </section>

            <?php if(session('cart') && count(session('cart')) > 0): ?>
                <!-- Order Summary -->
                <footer class="order-summary">
                    <?php
                        $cart = session('cart', []);
                        $subtotal = 0;
                        foreach ($cart as $item) {
                            $subtotal += ($item['price'] + ($item['addonsPrice'] ?? 0)) * $item['quantity'];
                        }
                        $discounts = 0;
                        $total = $subtotal - $discounts;
                    ?>

                    <div class="summary-row">
                        <span><strong>Sub Total:</strong></span>
                        <span id="subtotalAmount">PHP <?php echo e(number_format($subtotal, 2)); ?></span>
                    </div>

                    <?php if($discounts > 0): ?>
                        <div class="summary-row">
                            <span><strong>Discounts:</strong></span>
                            <span id="discountAmount">PHP <?php echo e(number_format($discounts, 2)); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="totalAmount">PHP <?php echo e(number_format($total, 2)); ?></span>
                    </div>

                    <div class="action-buttons">
                        <a href="<?php echo e(route('kiosk.main', ['orderType' => session('orderType', 'dine-in')])); ?>"
                            class="btn btn-back">
                            Back to Menu
                        </a>
                        <button type="button" class="btn btn-cancel" onclick="showCancelModal()">Cancel Order</button>
                        <button type="button" class="btn btn-pay" onclick="showPaymentModal()" id="payButton">PAY</button>
                    </div>
                </footer>
            <?php endif; ?>
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
                        PHP <?php echo e(number_format($total ?? 0, 2)); ?>

                    </div>
                </div>

                <div class="payment-methods">
                    <div class="payment-method-btn maya-method" onclick="selectPaymentMethod('maya')">
                        <span class="payment-method-icon">📱</span>
                        <div class="payment-method-title">Maya</div>
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
                    <button class="payment-modal-btn payment-modal-btn-cancel"
                        onclick="hidePaymentModal()">Cancel</button>
                    <button class="payment-modal-btn payment-modal-btn-proceed" id="proceedPaymentBtn"
                        onclick="proceedWithPayment()" disabled>
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- GCash Payment Modal -->
    <div class="payment-modal-overlay" id="gcashPaymentModal">
        <div class="payment-modal-container">
            <div class="payment-modal-header">
                <div class="payment-modal-icon">📱</div>
                <h2 class="payment-modal-title">Pay with MAYA</h2>
                <p class="payment-modal-subtitle">Complete your payment using MAYA</p>
            </div>

            <div class="payment-modal-content">
                <div class="payment-amount-display">
                    <div class="payment-amount-label">Amount to Pay:</div>
                    <div class="payment-amount-value" id="gcashPaymentAmount">
                        PHP <?php echo e(number_format($total ?? 0, 2)); ?>

                    </div>
                </div>

                <!-- GCash QR Code and Instructions -->
                <div class="gcash-payment-section">
                    <div class="gcash-qr-container" id="gcashQrContainer">
                        <!-- QR code or payment button will be inserted here -->
                    </div>

                    <div class="gcash-status-message" id="gcashStatusMessage">
                        <div class="status-icon">⏳</div>
                        <div class="status-text">Preparing payment...</div>
                    </div>

                    <div class="gcash-instructions">
                        <div class="instruction-title">📱 <strong>How to pay:</strong></div>
                        <div class="instruction-steps">
                            <div class="step">1. Scan the QR code and pay with your GCASH or MAYA</div>
                            <div class="step">2. Once you receive the digital receipt, take note of the
                                transaction/reference no.</div>
                            <div class="step">3. Show the number to the cashier to finalize order.</div>
                        </div>
                    </div>
                </div>

                <div class="payment-modal-actions">
                    <button class="payment-modal-btn payment-modal-btn-cancel" onclick="hideGCashModal()">
                        Cancel Payment
                    </button>
                    <button class="payment-modal-btn payment-modal-btn-proceed" id="gcashProceedBtn" disabled>
                        Pay Online
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Payment Confirmation Modal -->
    <div class="cash-confirmation-modal" id="cashConfirmationModal">
        <div class="cash-confirmation-container">
            <div class="cash-confirmation-header">
                <div class="payment-modal-icon">✅</div>
                <h2 class="payment-modal-title">Order Placed Successfully!</h2>
                <p class="payment-modal-subtitle">Your order has been prepared and is ready for payment</p>
            </div>

            <div class="cash-confirmation-content">
                <!-- Order Number Display -->
                <div class="order-number-display">
                    <div class="order-number-label">Your Order Number</div>
                    <div class="order-number-value" id="orderNumberDisplay">C001</div>
                    <div class="order-number-subtitle">Show this to the cashier</div>
                </div>

                <!-- Cashier Instructions -->
                <div class="cashier-instructions">
                    <div class="cashier-instruction-title">🗣️ <strong>Please tell the cashier:</strong></div>
                    <div class="cashier-instruction-text" id="cashierInstruction">
                        "Order #C001 - Cash Payment"
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="payment-details-box">
                    <div class="payment-detail-row">
                        <span>Order Total:</span>
                        <span id="confirmationTotal">PHP 0.00</span>
                    </div>
                    <div class="payment-detail-row change-highlight" id="confirmationChangeRow">
                        <span>💰 Your Change:</span>
                        <span id="confirmationChange">PHP 0.00</span>
                    </div>
                </div>

                <!-- Step-by-step Instructions -->
                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-text">Walk to the cashier counter with your order number</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-text" id="step2Text">Tell them: "Order #C001 - Cash Payment"</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-text" id="step3Text">Pay and collect your change</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-text">Wait for your order to be served</div>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <button class="confirmation-btn confirmation-btn-primary" onclick="completeOrder()">
                        Complete Order
                    </button>
                    <button class="confirmation-btn confirmation-btn-secondary" onclick="orderMore()">
                        Order More Items
                    </button>
                </div>

                <div class="footer-note">
                    Thank you for choosing Sip & Serve! 🍽️
                </div>
            </div>
        </div>
    </div>

    <!-- GCash Payment Success Modal -->
    <div class="cash-confirmation-modal" id="gcashSuccessModal">
        <div class="cash-confirmation-container">
            <div class="cash-confirmation-header">
                <div class="payment-modal-icon">✅</div>
                <h2 class="payment-modal-title">Payment Successful!</h2>
                <p class="payment-modal-subtitle">Your GCash payment has been processed successfully</p>
            </div>

            <div class="cash-confirmation-content">
                <!-- Order Number Display -->
                <div class="order-number-display">
                    <div class="order-number-label">Your Order Number</div>
                    <div class="order-number-value" id="gcashOrderNumberDisplay">G001</div>
                    <div class="order-number-subtitle">Your order is being prepared</div>
                </div>

                <!-- Payment Details -->
                <div class="payment-details-box">
                    <div class="payment-detail-row">
                        <span>Payment Method:</span>
                        <span>GCash</span>
                    </div>
                    <div class="payment-detail-row">
                        <span>Amount Paid:</span>
                        <span id="gcashAmountPaid">PHP 0.00</span>
                    </div>
                    <div class="payment-detail-row">
                        <span>Payment ID:</span>
                        <span id="gcashPaymentId">---</span>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <button class="confirmation-btn confirmation-btn-primary" onclick="completeOrder()">
                        Complete Order
                    </button>
                    <button class="confirmation-btn confirmation-btn-secondary" onclick="orderMore()">
                        Order More Items
                    </button>
                </div>

                <div class="footer-note">
                    Thank you for choosing Sip & Serve! 🍽️
                </div>
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
                <div class="cancel-modal-actions">
                    <button class="cancel-modal-btn cancel-modal-btn-no" onclick="hideCancelModal()">No, Keep
                        Order</button>
                    <button class="cancel-modal-btn cancel-modal-btn-yes" onclick="confirmCancelOrder()">Yes, Cancel
                        Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Error Modal -->
    <div class="payment-modal-overlay" id="paymentErrorModal">
        <div class="payment-modal-container">
            <div class="payment-modal-header">
                <div class="payment-modal-icon" style="color: #dc3545;">❌</div>
                <h2 class="payment-modal-title">Payment Failed</h2>
                <p class="payment-modal-subtitle">Your payment could not be processed</p>
            </div>

            <div class="payment-modal-content">
                <div class="payment-error-message" id="paymentErrorMessage">
                    Payment was not completed. Please try again or choose a different payment method.
                </div>

                <div class="payment-error-suggestions">
                    <h4>What you can do:</h4>
                    <ul>
                        <li>Try the GCash payment again</li>
                        <li>Switch to Cash payment instead</li>
                        <li>Check your GCash app for any issues</li>
                        <li>Contact our staff for assistance</li>
                    </ul>
                </div>

                <div class="payment-modal-actions">
                    <button class="payment-modal-btn payment-modal-btn-cancel" onclick="hidePaymentErrorModal()">
                        Close
                    </button>
                    <button class="payment-modal-btn payment-modal-btn-proceed"
                        onclick="hidePaymentErrorModal(); showPaymentModal();">
                        Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let cart = [];
        let selectedPaymentMethod = null;
        let totalAmount = <?php echo e($total ?? 0); ?>;
        let gcashPaymentIntentId = null;
        let gcashPollInterval = null;

        // Safely parse cart data
        try {
            const rawCart = <?php echo json_encode(session('cart', [])); ?>;
            cart = Array.isArray(rawCart) ? rawCart : Object.values(rawCart || {});
            console.log('Cart loaded:', cart.length, 'items');
        } catch (e) {
            console.error('Error parsing cart data:', e);
            cart = [];
        }

        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        }

        function updateItemQuantity(index, change) {
            fetch('<?php echo e(route("kiosk.updateCartItem")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
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
                fetch('<?php echo e(route("kiosk.removeCartItem")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
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

        function showPaymentModal() {
            console.log('showPaymentModal called, cart length:', cart.length);

            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const modal = document.getElementById('paymentModal');
            if (!modal) {
                console.error('Payment modal not found');
                return;
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Reset payment method selection
            selectedPaymentMethod = null;
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('selected');
            });

            const cashSection = document.getElementById('cashInputSection');
            const proceedBtn = document.getElementById('proceedPaymentBtn');

            if (cashSection) cashSection.classList.remove('show');
            if (proceedBtn) {
                proceedBtn.disabled = true;
                proceedBtn.textContent = 'Select Payment Method';
            }
        }

        function hidePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';

                // Reset form
                selectedPaymentMethod = null;
                const cashInput = document.getElementById('cashAmountInput');
                const changeDisplay = document.getElementById('changeDisplay');
                const cashSection = document.getElementById('cashInputSection');

                if (cashInput) cashInput.value = '';
                if (changeDisplay) changeDisplay.style.display = 'none';
                if (cashSection) cashSection.classList.remove('show');
            }
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;

            // Update UI
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('selected');
            });

            event.currentTarget.classList.add('selected');

            const proceedBtn = document.getElementById('proceedPaymentBtn');
            const cashSection = document.getElementById('cashInputSection');

            if (method === 'gcash' || method === 'maya') {
                if (cashSection) cashSection.classList.remove('show');
                if (proceedBtn) {
                    proceedBtn.disabled = false;
                    proceedBtn.textContent = method === 'maya' ? 'Pay with Maya' : 'Pay with GCash';
                }
            } else if (method === 'cash') {
                if (cashSection) cashSection.classList.add('show');
                if (proceedBtn) {
                    proceedBtn.disabled = true;
                    proceedBtn.textContent = 'Enter Cash Amount';
                }
                const cashInput = document.getElementById('cashAmountInput');
                if (cashInput) cashInput.focus();
            }
        }

        function calculateChange() {
            const cashInput = document.getElementById('cashAmountInput');
            const changeDisplay = document.getElementById('changeDisplay');
            const changeAmount = document.getElementById('changeAmount');
            const proceedBtn = document.getElementById('proceedPaymentBtn');

            if (!cashInput || !changeDisplay || !changeAmount || !proceedBtn) return;

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

            if (selectedPaymentMethod === 'cash') {
                processCashPayment();
            } else if (selectedPaymentMethod === 'gcash' || selectedPaymentMethod === 'maya') {
                processGCashPayment();
            }
        }

        // MAYA Payment Functions
        function processGCashPayment() {
            hidePaymentModal();
            showGCashModal();

            // Show static QR code immediately
            updateGCashStatus('📱', 'Ready to pay with Maya');
            showStaticQRCode();
        }

        function showGCashModal() {
            const modal = document.getElementById('gcashPaymentModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function hideGCashModal() {
            const modal = document.getElementById('gcashPaymentModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }

            // Stop polling
            if (gcashPollInterval) {
                clearInterval(gcashPollInterval);
                gcashPollInterval = null;
            }
        }

        function showStaticQRCode() {
            const qrContainer = document.getElementById('gcashQrContainer');
            const proceedBtn = document.getElementById('gcashProceedBtn');

            if (qrContainer) {
                qrContainer.innerHTML = `
            <div class="static-qr-container">
                <img src="/assets/maya-qr.png" alt="Maya QR Code" style="width: 250px; height: 250px; border: 2px solid #007bff; border-radius: 10px;">
                <p style="margin-top: 15px; font-weight: bold; color: #007bff;">Scan to pay with Maya</p>
            </div>
        `;
            }

            if (proceedBtn) {
                proceedBtn.disabled = false;
                proceedBtn.textContent = 'FINISH PAYMENT';
                proceedBtn.onclick = () => showFinishPaymentModal();
            }
        }

        function confirmManualPayment() {
            if (confirm('Have you completed the payment via Maya? Please ensure payment is sent before confirming.')) {
                // Process as cash payment for now, or create separate endpoint
                processCashPayment();
            }
        }
        function showFinishPaymentModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay show';
            modal.id = 'finishPaymentModal';
            modal.innerHTML = `
        <div class="payment-modal-container" style="max-width: 400px;">
            <div class="payment-modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h2 style="color: white; margin: 0;">Payment Complete?</h2>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-top: 8px;">Total: PHP ${totalAmount.toFixed(2)}</p>
            </div>
            <div class="payment-modal-content" style="padding: 30px;">
                <div style="text-align: center; margin: 20px 0;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">💳</div>
                    <p style="font-size: 1.1rem; font-weight: 600; color: #333;">
                        Have you completed the Maya payment?
                    </p>
                    <p style="font-size: 0.9rem; color: #666; margin-top: 10px;">
                        Make sure you received the digital receipt
                    </p>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button onclick="hideFinishPaymentModal()" style="background: #6c757d; color: white; border: none; padding: 15px 25px; border-radius: 8px; cursor: pointer; flex: 1; font-size: 1rem; font-weight: 600;">
                        NO - Go Back
                    </button>
                    <button onclick="confirmMayaPaymentComplete()" style="background: #28a745; color: white; border: none; padding: 15px 25px; border-radius: 8px; cursor: pointer; flex: 1; font-size: 1rem; font-weight: 600;">
                        YES - Complete
                    </button>
                </div>
            </div>
        </div>
    `;

            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
        }

        async function confirmMayaPaymentComplete() {
            try {
                console.log('=== MAYA PAYMENT STARTED ===');

                hideFinishPaymentModal();
                hideGCashModal();

                const gcashBtn = document.getElementById('gcashProceedBtn');
                if (gcashBtn) {
                    gcashBtn.disabled = true;
                    gcashBtn.textContent = 'Processing...';
                }

                // Send to the SAME endpoint with payment_method explicitly set
                const orderData = {
                    cash_amount: parseFloat(totalAmount),
                    payment_method: 'maya'  // THIS MUST BE SENT
                };

                console.log('Sending to server:', orderData);

                const response = await fetch('/kiosk/process-maya-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(orderData)
                });

                const data = await response.json();
                console.log('Server response:', data);

                if (data.success) {
                    showMayaSuccessConfirmation(data);
                } else {
                    throw new Error(data.message || 'Failed to process Maya payment');
                }

            } catch (error) {
                console.error('Maya payment error:', error);
                alert('Failed to complete Maya payment: ' + error.message);

                const gcashBtn = document.getElementById('gcashProceedBtn');
                if (gcashBtn) {
                    gcashBtn.disabled = false;
                    gcashBtn.textContent = 'FINISH PAYMENT';
                }
                showGCashModal();
            }
        }

        
        function showMayaSuccessConfirmation(data) {
            const modal = document.getElementById('cashConfirmationModal');
            if (!modal) {
                console.error('Confirmation modal not found');
                return;
            }

            const orderNumber = data.order_number || 'M001';

            // Update order number display
            const orderNumberDisplay = document.getElementById('orderNumberDisplay');
            if (orderNumberDisplay) orderNumberDisplay.textContent = orderNumber;

            // Update cashier instruction
            const cashierInstruction = document.getElementById('cashierInstruction');
            if (cashierInstruction) {
                cashierInstruction.textContent = `"Order #${orderNumber} - Maya Payment"`;
            }

            // Update steps
            const step2Text = document.getElementById('step2Text');
            const step3Text = document.getElementById('step3Text');
            if (step2Text) step2Text.textContent = `Tell them: "Order #${orderNumber} - Maya Payment"`;
            if (step3Text) step3Text.textContent = `Show your Maya receipt with reference number`;

            // Update payment amount
            const totalAmountFromData = parseFloat(data.total_amount) || totalAmount;
            const confirmationTotal = document.getElementById('confirmationTotal');
            if (confirmationTotal) confirmationTotal.textContent = `PHP ${totalAmountFromData.toFixed(2)}`;

            // Update change display
            const confirmationChange = document.getElementById('confirmationChange');
            if (confirmationChange) confirmationChange.textContent = '✅ Paid via Maya';

            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function hideFinishPaymentModal() {
            const modal = document.getElementById('finishPaymentModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = '';
            }
        }

        function completePaymentAndReturn() {
            // Create order with Maya payment method
            const orderData = {
                cash_amount: totalAmount,
                payment_method: 'maya'  // FIXED: Simplified data structure
            };

            console.log('Completing payment with data:', orderData);

            // Show loading state
            const yesBtn = event.target;
            const originalText = yesBtn.textContent;
            yesBtn.disabled = true;
            yesBtn.textContent = 'Processing...';

            fetch('/kiosk/process-maya-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(orderData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error response:', text);
                            throw new Error(`Server error: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Maya payment response:', data);
                    if (data.success) {
                        hideFinishPaymentModal();
                        hideGCashModal();
                        window.location.href = '<?php echo e(route("kiosk.index")); ?>';
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Order processing error:', error);
                    alert('Error processing order: ' + error.message);
                    yesBtn.disabled = false;
                    yesBtn.textContent = originalText;
                });
        }

        /////////////////////////

        function openGCashPayment(url) {
            // Open payment in new tab/window
            window.location.href = url;

            updateGCashStatus('⏳', 'Payment opened in new window. Please complete the payment...');

            // Listen for window close (optional)
            const checkClosed = setInterval(() => {
                if (paymentWindow.closed) {
                    clearInterval(checkClosed);
                    updateGCashStatus('🔄', 'Checking payment status...');
                }
            }, 1000);
        }

        function checkGCashPaymentStatus(paymentIntentId) {
            fetch(`/api/pos/payment/status/${paymentIntentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const status = data.status;

                        switch (status) {
                            case 'succeeded':
                                onGCashPaymentSuccess(paymentIntentId, data.data);
                                break;
                            case 'failed':
                            case 'cancelled':
                                onGCashPaymentFailed(status);
                                break;
                            case 'processing':
                                updateGCashStatus('⏳', 'Processing payment...');
                                break;
                        }
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                });
        }

        function onGCashPaymentSuccess(paymentIntentId, paymentData) {
            // Stop polling
            if (gcashPollInterval) {
                clearInterval(gcashPollInterval);
                gcashPollInterval = null;
            }

            // Clear cart and redirect to your order confirmation success page
            fetch('/kiosk/process-gcash-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId,
                    payment_data: paymentData
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to your order confirmation success page
                        window.location.href = data.redirect_url || '<?php echo e(route("kiosk.orderConfirmationSuccess")); ?>';
                    } else {
                        hideGCashModal();
                        alert('Payment was successful but there was an error processing your order. Please contact support.');
                    }
                })
                .catch(error => {
                    console.error('Order processing error:', error);
                    hideGCashModal();
                    alert('Payment was successful but there was an error processing your order. Please contact support.');
                });
        }

        function onGCashPaymentFailed(status) {
            // Stop polling
            if (gcashPollInterval) {
                clearInterval(gcashPollInterval);
                gcashPollInterval = null;
            }

            updateGCashStatus('❌', `Payment ${status}. Please try again.`);
        }

        function updateGCashStatus(icon, message) {
            const statusIcon = document.querySelector('#gcashStatusMessage .status-icon');
            const statusText = document.querySelector('#gcashStatusMessage .status-text');

            if (statusIcon) statusIcon.textContent = icon;
            if (statusText) statusText.textContent = message;
        }

        function showGCashSuccessModal(paymentIntentId, paymentData) {
            const modal = document.getElementById('gcashSuccessModal');
            if (!modal) return;

            // Update order number and payment details
            const orderNumber = paymentData.attributes?.metadata?.order_id || 'G' + Date.now().toString().slice(-3);

            const orderNumberDisplay = document.getElementById('gcashOrderNumberDisplay');
            const amountPaid = document.getElementById('gcashAmountPaid');
            const paymentId = document.getElementById('gcashPaymentId');

            if (orderNumberDisplay) orderNumberDisplay.textContent = orderNumber;
            if (amountPaid) amountPaid.textContent = `PHP ${totalAmount.toFixed(2)}`;
            if (paymentId) paymentId.textContent = paymentIntentId.substring(0, 20) + '...';

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Cash payment functions (existing code)
        function processCashPayment() {
            const cashAmountInput = document.getElementById('cashAmountInput');
            if (!cashAmountInput) {
                alert('Please enter cash amount');
                return;
            }

            const cashAmount = parseFloat(cashAmountInput.value);
            if (isNaN(cashAmount) || cashAmount < totalAmount) {
                alert('Insufficient or invalid cash amount.');
                return;
            }

            const proceedBtn = document.getElementById('proceedPaymentBtn');
            const originalText = proceedBtn ? proceedBtn.textContent : '';
            if (proceedBtn) {
                proceedBtn.disabled = true;
                proceedBtn.textContent = 'Processing...';
            }

            // FIXED: Explicitly set payment_method to 'cash'
            const orderData = {
                cash_amount: cashAmount,
                payment_method: 'cash'  // Explicitly set as cash
            };

            console.log('Sending cash payment request:', orderData);
            console.log('Payment method:', orderData.payment_method);

            fetch('/kiosk/process-cash-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(orderData)
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error response:', text);
                            try {
                                const errorData = JSON.parse(text);
                                throw new Error(errorData.message || `Server error: ${response.status}`);
                            } catch (e) {
                                throw new Error(`Server error: ${response.status}`);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success response:', data);
                    console.log('Order payment method:', data.payment_method);
                    if (data.success) {
                        hidePaymentModal();
                        showCashPaymentConfirmation(data);
                    } else {
                        alert('Error processing cash payment: ' + (data.message || 'Unknown error'));
                        if (proceedBtn) {
                            proceedBtn.disabled = false;
                            proceedBtn.textContent = originalText;
                        }
                    }
                })
                .catch(error => {
                    console.error('Payment Error:', error);
                    alert('Error processing payment: ' + error.message);
                    if (proceedBtn) {
                        proceedBtn.disabled = false;
                        proceedBtn.textContent = originalText;
                    }
                });
        }

        function showCashPaymentConfirmation(data) {
            const modal = document.getElementById('cashConfirmationModal');
            if (!modal) {
                console.error('Cash confirmation modal not found');
                return;
            }

            // Update order number displays
            const orderNumber = data.order_number || 'C001';
            const orderNumberDisplay = document.getElementById('orderNumberDisplay');
            if (orderNumberDisplay) orderNumberDisplay.textContent = orderNumber;

            // Update cashier instruction
            const cashierInstruction = document.getElementById('cashierInstruction');
            if (cashierInstruction) cashierInstruction.textContent = `"Order #${orderNumber} - Cash Payment"`;

            // Update step instructions
            const step2Text = document.getElementById('step2Text');
            const step3Text = document.getElementById('step3Text');
            if (step2Text) step2Text.textContent = `Tell them: "Order #${orderNumber} - Cash Payment"`;
            if (step3Text) step3Text.textContent = `Pay and collect your change if needed`;

            // Update payment amounts
            const totalAmountFromData = parseFloat(data.total_amount) || totalAmount;
            const cashAmount = parseFloat(data.cash_amount) || 0;
            const changeAmount = parseFloat(data.change_amount) || 0;

            const confirmationTotal = document.getElementById('confirmationTotal');
            const confirmationCash = document.getElementById('confirmationCash');
            const confirmationChange = document.getElementById('confirmationChange');

            if (confirmationTotal) confirmationTotal.textContent = `PHP ${totalAmountFromData.toFixed(2)}`;
            if (confirmationCash) confirmationCash.textContent = `PHP ${cashAmount.toFixed(2)}`;
            if (confirmationChange) confirmationChange.textContent = `PHP ${changeAmount.toFixed(2)}`;

            // Show/hide change row if no change needed
            const changeRow = document.getElementById('confirmationChangeRow');
            if (changeRow) {
                changeRow.style.display = changeAmount <= 0 ? 'none' : 'flex';
            }

            // Show the modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function orderMore() {
            window.location.href = '<?php echo e(route("kiosk.main")); ?>';
        }

        function completeOrder() {
            window.location.href = '<?php echo e(route("kiosk.index")); ?>';
            //must include here the payment_method -> 'maya' to be added to the order placed
        }

        function showCancelModal() {
            const modal = document.getElementById('cancelModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        function confirmCancelOrder() {
            fetch('<?php echo e(route("kiosk.cancelOrder")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '<?php echo e(route("kiosk.index")); ?>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '<?php echo e(route("kiosk.index")); ?>';
                });
        }

        // Payment Error Modal Functions
        function showPaymentErrorModal(message) {
            const modal = document.getElementById('paymentErrorModal');
            const messageElement = document.getElementById('paymentErrorMessage');

            if (messageElement && message) {
                messageElement.textContent = message;
            }

            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function hidePaymentErrorModal() {
            const modal = document.getElementById('paymentErrorModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing...');

            // Check for payment error from Laravel session
            <?php if(session('payment_error')): ?>
                showPaymentErrorModal('<?php echo e(session("payment_error")); ?>');
            <?php endif; ?>
            
            const cashInput = document.getElementById('cashAmountInput');
            if (cashInput) {
                cashInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        const proceedBtn = document.getElementById('proceedPaymentBtn');
                        if (proceedBtn && !proceedBtn.disabled) {
                            proceedWithPayment();
                        }
                    }
                });
            }
        });

        // Global exports (add to bottom of script)
        window.showFinishPaymentModal = showFinishPaymentModal;
        window.hideFinishPaymentModal = hideFinishPaymentModal;
        window.confirmMayaPaymentComplete = confirmMayaPaymentComplete;
        window.showMayaSuccessConfirmation = showMayaSuccessConfirmation;
    </script>
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/kioskOrderConfirmation.blade.php ENDPATH**/ ?>