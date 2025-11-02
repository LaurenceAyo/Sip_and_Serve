<!DOCTYPE html>
<html lang="en">

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Pusher Configuration for Real-time Notifications -->
    
    <?php
        // Safely read nested broadcasting.connection values without causing "Config [...] not found"
        $pusherConnections = config('broadcasting.connections', []);
        $pusherKey = data_get($pusherConnections, 'pusher.key', env('PUSHER_APP_KEY', ''));
        $pusherCluster = data_get($pusherConnections, 'pusher.options.cluster', env('PUSHER_APP_CLUSTER', ''));
    ?>
    <meta name="pusher-key" content="<?php echo e($pusherKey); ?>">
    <meta name="pusher-cluster" content="<?php echo e($pusherCluster); ?>">

    <!-- Pusher Library -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sip & Serve - Cashier</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f1e8, #F5E6D3);
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            text-align: center;
            padding: 25px;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 3px;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            position: relative;
        }

        .header::before {
            content: '☕';
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
        }

        .header::after {
            content: ;
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
        }

        .printer-controls {
            position: absolute;
            left: 100px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 10px;
        }

        .printer-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 8px 12px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .printer-btn:hover {
            background: white;
            color: #8b4513;
        }

        .refresh-button {
            position: absolute;
            right: 100px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .refresh-button:hover {
            background: white;
            color: #8b4513;
        }

        .container {
            display: flex;
            height: calc(100vh - 100px);
        }

        .left-panel {
            flex: 1;
            background: #f5f1e8;
            padding: 25px;
            border-right: 3px solid #d4c4a8;
            overflow-y: auto;
        }

        .right-panel {
            flex: 1;
            background: #F5E6D3;
            padding: 25px;
            overflow-y: auto;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 10px;
            border-bottom: 3px solid #8b4513;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: '📋';
            font-size: 1.2rem;
        }

        .order-card {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.15);
            transition: all 0.3s ease;
            position: relative;
        }

        .order-card:hover {
            border-color: #8b4513;
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.25);
            transform: translateY(-2px);
        }

        .order-card.selected {
            border-color: #27ae60;
            background: linear-gradient(135deg, #f8fff9, #e8f5e8);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        }

        .order-header {
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #2c1810;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-number {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .order-time {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-time::before {
            content: '⏰';
            font-size: 1rem;
        }

        .order-type {
            background: #e8f4fd;
            color: #2c3e50;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-items {
            margin: 15px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 8px 0;
            padding: 8px 0;
            color: #2c1810;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
            font-weight: 500;
        }

        .item-price {
            font-weight: bold;
            color: #8b4513;
            font-size: 1.05rem;
        }

        .order-total {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #2c1810;
            font-size: 1.1rem;
        }

        .order-total .total-amount {
            color: #8b4513;
            font-size: 1.2rem;
        }

        .cash-details {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }

        .cash-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            color: #856404;
            font-weight: 600;
        }

        .cash-row.change {
            border-top: 1px solid #ffeaa7;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 1.1rem;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 90px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-accept {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .btn-accept:hover:not(:disabled) {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .btn-edit:hover:not(:disabled) {
            background: linear-gradient(135deg, #e67e22, #d35400);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-cancel:hover:not(:disabled) {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        .processing-section {
            background: white;
            border: 3px solid #d4c4a8;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            line-height: 1.8;
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.1);
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .processing-section.has-order {
            text-align: left;
            padding: 30px;
        }

        .processing-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .processing-order-header {
            background: linear-gradient(135deg, #f0c8ab, #a0522d);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }

        .processing-order-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .processing-order-time {
            font-size: 1rem;
            opacity: 0.9;
        }

        .processing-items {
            margin-bottom: 25px;
        }

        .processing-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #2c1810;
        }

        .processing-item:last-child {
            border-bottom: none;
        }

        .processing-total {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            margin-bottom: 25px;
        }

        .processing-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .processing-total-final {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.3rem;
            color: #8b4513;
        }

        .processing-actions {
            display: flex;
            gap: 15px;
        }

        .btn-large {
            padding: 15px 25px;
            font-size: 1.1rem;
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #0066cc;
        }

        .empty-state {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 40px 20px;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 15px;
            margin-top: 20px;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Enhanced Payment Modal */
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
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 25px 50px rgba(139, 69, 19, 0.4);
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .modal-header.cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .modal-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .modal-content {
            padding: 30px 25px;
        }

        .payment-form {
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 3px solid #d4c4a8;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-input.error {
            border-color: #e74c3c;
            background: #fdf2f2;
        }

        .payment-summary {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .summary-row.total {
            border-top: 2px solid #d4c4a8;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.3rem;
            color: #8b4513;
        }

        .summary-row.change {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px -5px 10px;
            font-weight: bold;
            font-size: 1.4rem;
            color: #155724;
        }

        .summary-row.change.negative {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .modal-message {
            font-size: 1.1rem;
            color: #2c1810;
            line-height: 1.5;
            margin-bottom: 25px;
            text-align: center;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            min-width: 120px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
        }

        .modal-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .modal-btn-cancel:hover:not(:disabled) {
            background: #5a6268;
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .modal-btn-confirm:hover:not(:disabled) {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-1px);
        }

        .modal-btn-confirm.cancel-style {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .modal-btn-confirm.cancel-style:hover:not(:disabled) {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .modal-btn-confirm.processing {
            background: #6c757d;
            cursor: not-allowed;
        }

        .modal-btn-confirm.processing::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: button-loading-spinner 1s ease infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes button-loading-spinner {
            from {
                transform: translate(-50%, -50%) rotate(0turn);
            }

            to {
                transform: translate(-50%, -50%) rotate(1turn);
            }
        }

        .customer-payment-info {
            background: #e3f2fd;
            border: 2px solid #90caf9;
            border-radius: 10px;
            padding: 12px 15px;
            margin: 15px 0;
            color: #0d47a1;
        }

        .payment-info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .payment-info-row.expected-change {
            border-top: 1px solid #90caf9;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 1rem;
            color: #1565c0;
        }

        .payment-info-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-amount-note {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 0.85rem;
            color: #e65100;
            text-align: center;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .loading.show {
            display: block;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #8b4513;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Quick cash buttons */
        .quick-cash-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .quick-cash-btn {
            padding: 10px;
            border: 2px solid #d4c4a8;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            color: #2c1810;
            transition: all 0.3s ease;
        }

        .quick-cash-btn:hover {
            background: #8b4513;
            color: white;
            border-color: #8b4513;
        }

        /* Edit Order Modal Styles */
        .edit-modal {
            background: white;
            width: 95%;
            max-width: 1200px;
            height: 90vh;
            border-radius: 15px;
            box-shadow: 0 25px 50px rgba(139, 69, 19, 0.4);
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
            display: flex;
            flex-direction: column;
        }

        .edit-modal-header {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 20px 25px;
            text-align: center;
            position: relative;
        }

        .edit-modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .edit-modal-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .edit-modal-close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .edit-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) rotate(90deg);
        }

        .edit-modal-content {
            flex: 1;
            padding: 25px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .edit-order-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            height: 100%;
            min-height: 0;
        }

        .edit-order-section {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .edit-section-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4c4a8;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-items-list {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }

        .order-items-list::-webkit-scrollbar {
            width: 6px;
        }

        .order-items-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .order-items-list::-webkit-scrollbar-thumb {
            background: #8b4513;
            border-radius: 3px;
        }

        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .logout-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-modal {
            background: white;
            border-radius: 10px;
            padding: 25px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.8);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logout-modal-overlay.show .logout-modal {
            transform: scale(1);
        }

        .logout-modal h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.3rem;
        }

        .logout-modal p {
            margin-bottom: 25px;
            color: #666;
            font-size: 1rem;
        }

        .logout-modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .logout-modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .logout-modal-btn-cancel:hover {
            background: #5a6268;
        }

        .logout-modal-btn-confirm {
            background: #dc3545;
            color: white;
        }

        .logout-modal-btn-confirm:hover {
            background: #c82333;
        }

        .edit-order-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border: 2px solid #d4c4a8;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .edit-order-item:hover {
            border-color: #8b4513;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.2);
        }

        .edit-item-info {
            flex: 1;
            margin-right: 15px;
        }

        .edit-item-name {
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .edit-item-price {
            color: #8b4513;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .edit-item-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .edit-quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #e9ecef;
        }

        .edit-quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: #8b4513;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .edit-quantity-btn:hover {
            background: #6d3410;
            transform: scale(1.1);
        }

        .edit-quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .edit-quantity-display {
            font-weight: 700;
            color: #2c1810;
            min-width: 30px;
            text-align: center;
            font-size: 1.1rem;
        }

        .edit-item-remove {
            width: 36px;
            height: 36px;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .edit-item-remove:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .menu-items-list {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }

        .menu-search {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #d4c4a8;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 15px;
            transition: border-color 0.3s ease;
        }

        .menu-search:focus {
            outline: none;
            border-color: #8b4513;
        }

        .edit-menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .edit-menu-item:hover {
            border-color: #28a745;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }

        .edit-menu-item-info {
            flex: 1;
        }

        .edit-menu-item-name {
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 3px;
            font-size: 1rem;
        }

        .edit-menu-item-price {
            color: #28a745;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .edit-add-btn {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-add-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .edit-add-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .edit-modal-footer {
            background: #f8f9fa;
            border-top: 2px solid #e9ecef;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-total-display {
            font-size: 1.3rem;
            font-weight: bold;
            color: #8b4513;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            border: 2px solid #d4c4a8;
        }

        .edit-modal-actions {
            display: flex;
            gap: 15px;
        }

        .edit-modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .edit-modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .edit-modal-btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .edit-modal-btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .edit-modal-btn-save:hover {
            background: linear-gradient(135deg, #218838, #1fa970);
            transform: translateY(-1px);
        }

        .empty-order-state {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px 20px;
        }

        .empty-order-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Printer status indicator */
        .printer-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 1000;
            display: none;
        }

        .logout-section {
            position: fixed;
            right: 20px;
            top: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .printer-status.show {
            display: block;
        }

        .printer-status.success {
            background: rgba(40, 167, 69, 0.9);
        }

        .printer-status.error {
            background: rgba(220, 53, 69, 0.9);
        }

        .printer-status.warning {
            background: rgba(255, 193, 7, 0.9);
            color: #212529;
        }

        .printer-status.processing {
            background: rgba(108, 117, 125, 0.9);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .left-panel,
            .right-panel {
                flex: none;
                height: auto;
            }

            .order-actions {
                flex-direction: column;
            }

            .btn {
                flex: none;
                width: 100%;
            }

            .edit-order-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .printer-controls {
                position: static;
                transform: none;
                margin-top: 10px;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px;
                font-size: 1.8rem;
            }

            .header::before,
            .header::after {
                display: none;
            }

            .left-panel,
            .right-panel {
                padding: 15px;
            }

            /* Add to your existing CSS */
            @keyframes blink {

                0%,
                20% {
                    opacity: 0.2;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    opacity: 0.2;
                }
            }

            @keyframes highlightGreen {
                0% {
                    background-color: #c8e6c9;
                }

                50% {
                    background-color: #a5d6a7;
                }

                100% {
                    background-color: #c8e6c9;
                }
            }

            @keyframes slideInFromRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            .loading-dots span {
                font-size: 24px;
                color: #856404;
            }

            .maya-reference-box {
                animation: highlightGreen 2s ease-in-out;
            }

            @keyframes blink {

                0%,
                20%,
                50%,
                80%,
                100% {
                    opacity: 1;
                }

                40% {
                    opacity: 0.3;
                }

                60% {
                    opacity: 0.5;
                }
            }

            .processing-actions {
                flex-direction: column;
            }

            .edit-modal {
                width: 98%;
                height: 95vh;
            }

            .edit-modal-content {
                padding: 15px;
            }

            .printer-controls {
                flex-direction: column;
                gap: 5px;
            }
        }

        .payment-method-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 8px;
        }

        .payment-cash {
            background-color: #28a745;
            color: white;
        }

        .payment-maya {
            background-color: #007bff;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            font-size: 0.9rem;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
    </style>
</head>

<body>

    <div class="header">
        SIP & SERVE - CASHIER

        <!-- Printer Test Controls -->
        <div class="printer-controls">
            <button class="printer-btn" onclick="testPrinterConnection()">
                🖨️ Test Printer
            </button>
            <button class="printer-btn" onclick="showPrinterInfo()">
                ℹ️ Printer Info
            </button>
        </div>
        <!-- Add this logout section -->
        <div class="logout-section">
            <div class="user-info">
            </div>
            <button class="logout-btn" onclick="logout()">
                <span>🚪</span>
                <span>Logout</span>
            </button>
        </div>
    </div>

    <div class="container">
        <!-- Left Panel - Pending Orders -->
        <div class="left-panel">
            <h2 class="section-title">Pending Orders</h2>

            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p>Loading orders...</p>
            </div>

            <div id="ordersContainer">
                <?php if(isset($pendingOrders) && count($pendingOrders) > 0): ?>
                    <?php $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-card" id="order-<?php echo e($order['id']); ?>" data-order-id="<?php echo e($order['id']); ?>">
                            <div class="order-header">
                                <span>Order</span>
                                <span class="order-number">#<?php echo e($order['id']); ?></span>
                            </div>
                            <div class="order-time">
                                Placed at <?php echo e($order['time']); ?>

                                <span class="order-type"><?php echo e(ucfirst($order['order_type'] ?? 'dine-in')); ?></span>
                            </div>
                            <div class="status-badge status-pending">Pending Payment</div>
                            <div
                                class="payment-method-badge <?php echo e($order['payment_method'] === 'maya' ? 'payment-maya' : 'payment-cash'); ?>">
                                <?php echo e(strtoupper($order['payment_method'])); ?>

                            </div>

                            <!-- ADD THE MAYA REFERENCE DATA HERE AS HIDDEN FIELDS -->
                            <input type="hidden" class="maya-reference" value="<?php echo e($order['maya_reference'] ?? ''); ?>">
                            <input type="hidden" class="maya-webhook-time"
                                value="<?php echo e($order['maya_webhook_received_at'] ?? ''); ?>">
                            <div class="order-items">
                                <?php if(isset($order['order_items']) && is_array($order['order_items'])): ?>
                                    <?php $__currentLoopData = $order['order_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="order-item">
                                            <span class="item-name"><?php echo e($item['name']); ?> x<?php echo e($item['quantity']); ?></span>
                                            <span class="item-price">PHP <?php echo e(number_format($item['total_price'], 2)); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php elseif(isset($order['items']) && is_array($order['items'])): ?>
                                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="order-item">
                                            <span class="item-name"><?php echo e($item['name']); ?></span>
                                            <span class="item-price">PHP <?php echo e(number_format($item['price'], 2)); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>

                            <?php if((isset($order['cash_amount']) && $order['cash_amount'] > 0) || (isset($order['payment_method']) && $order['payment_method'] === 'cash')): ?>
                                <div class="customer-payment-info">
                                    <div
                                        style="font-weight: 700; margin-bottom: 8px; color: #1565c0; display: flex; align-items: center; gap: 8px;">
                                        💰 Customer's Payment Plan
                                    </div>
                                    <div class="payment-info-row">
                                        <span class="payment-info-label">🏷️ Order Total:</span>
                                        <span>PHP <?php echo e(number_format($order['total'] ?? $order['total_amount'] ?? 0, 2)); ?></span>
                                    </div>
                                    <?php if(isset($order['cash_amount']) && $order['cash_amount'] > 0): ?>
                                        <div class="payment-info-row">
                                            <span class="payment-info-label">💵 Will Bring:</span>
                                            <span>PHP <?php echo e(number_format($order['cash_amount'], 2)); ?></span>
                                        </div>
                                        <?php if(isset($order['expected_change']) && $order['expected_change'] > 0): ?>
                                            <div class="payment-info-row expected-change">
                                                <span class="payment-info-label">💸 Expected Change:</span>
                                                <span>PHP <?php echo e(number_format($order['expected_change'], 2)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="payment-info-row">
                                            <span class="payment-info-label">💵 Will Bring:</span>
                                            <span style="color: #666; font-style: italic;">Amount to be determined</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="edit-amount-note">
                                        💡 You can edit the cash amount during payment processing
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="order-total">
                                <span>Total Amount:</span>
                                <span class="total-amount">PHP
                                    <?php echo e(number_format($order['total'] ?? $order['total_amount'] ?? 0, 2)); ?></span>
                            </div>

                            <div class="order-actions">
                                <button class="btn btn-accept"
                                    onclick="acceptOrder('<?php echo e($order['id']); ?>', <?php echo e($order['total'] ?? $order['total_amount'] ?? 0); ?>, '<?php echo e($order['order_number'] ?? $order['id']); ?>', <?php echo e($order['cash_amount'] ?? 0); ?>)">
                                    ✅ Accept
                                </button>
                                <button class="btn btn-edit" onclick="editOrder('<?php echo e($order['id']); ?>')">
                                    ✏️ Edit
                                </button>
                                <button class="btn btn-cancel" onclick="cancelOrder('<?php echo e($order['id']); ?>')">
                                    ❌ Cancel
                                </button>
                                <?php if($order['payment_method'] === 'maya'): ?>
                                    <div class="maya-payment-section"
                                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: 2px solid #1976d2; border-radius: 10px; padding: 15px; margin: 15px 0;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                            <span style="font-size: 24px;">📱</span>
                                            <strong style="color: #1565c0; font-size: 1.1rem;">Maya QR Payment</strong>
                                        </div>

                                        <?php if(isset($order['maya_reference']) && $order['maya_reference']): ?>
                                            <!-- Reference Received -->
                                            <div class="maya-reference-box"
                                                style="background: #c8e6c9; border: 2px solid #388e3c; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                                    <span style="font-size: 20px;">✅</span>
                                                    <strong style="color: #2e7d32;">Payment Received!</strong>
                                                </div>

                                                <div
                                                    style="background: white; padding: 10px; border-radius: 6px; font-family: 'Courier New', monospace; text-align: center; margin-bottom: 10px;">
                                                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 4px;">Reference Number:
                                                    </div>
                                                    <div
                                                        style="font-size: 1.2rem; font-weight: bold; color: #1976d2; letter-spacing: 1px;">
                                                        <?php echo e($order['maya_reference']); ?>

                                                    </div>
                                                </div>

                                                <div style="font-size: 0.85rem; color: #555; text-align: center; margin-bottom: 10px;">
                                                    💡 Ask customer to show their Maya receipt<br>
                                                    <strong>Verify the reference number matches</strong>
                                                </div>

                                                <button class="btn btn-confirm-maya"
                                                    onclick="quickConfirmMaya(<?php echo e($order['id']); ?>, '<?php echo e($order['order_number']); ?>', '<?php echo e($order['maya_reference']); ?>')"
                                                    style="width: 100%; background: #2e7d32; color: white; padding: 12px; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer;">
                                                    ✅ Confirm Payment Match
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <!-- Waiting for Payment -->
                                            <div class="maya-waiting-box"
                                                style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 12px; text-align: center;">
                                                <div style="font-size: 1.5rem; margin-bottom: 8px;">⏳</div>
                                                <div style="font-weight: 600; color: #856404; margin-bottom: 6px;">
                                                    Waiting for Payment...
                                                </div>
                                                <div style="font-size: 0.85rem; color: #856404;">
                                                    Customer is scanning QR code<br>
                                                    Reference will appear here automatically
                                                </div>

                                                <div class="loading-dots" style="margin-top: 10px;">
                                                    <span style="animation: blink 1.4s infinite;">●</span>
                                                    <span style="animation: blink 1.4s infinite 0.2s;">●</span>
                                                    <span style="animation: blink 1.4s infinite 0.4s;">●</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <!-- Empty state when no orders -->
                    <div class="empty-state" id="emptyState">
                        <div class="empty-state-icon">📭</div>
                        <p>No pending cash orders</p>
                        <small>Orders will appear here when customers place cash orders</small>
                    </div>
                <?php endif; ?>
            </div>
            </div>
       

        <!-- Right Panel - Processing Order Section -->
        <div class="right-panel">
            <h2 class="section-title">Processing Payment</h2>

            <div class="processing-section" id="processingSection">
                <div class="processing-icon">💳</div>
                <p><strong>SELECT AN ORDER</strong></p>
                <p>FROM THE LEFT PANEL TO</p>
                <p>BEGIN PROCESSING PAYMENT</p>
                <br>
                <p>Use the <strong>Accept</strong> button to process payment</p>
                <p>Use the <strong>Edit</strong> button to modify orders</p>
                <p>Use the <strong>Cancel</strong> button to cancel orders</p>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <div class="modal-header" id="paymentModalHeader">
                <div class="modal-icon">💰</div>
                <h2 class="modal-title">Process Payment</h2>
                <p class="modal-subtitle">Calculate change and complete transaction</p>
            </div>

            <div class="modal-content">
                <div class="payment-form">
                    <div class="form-group">
                        <label class="form-label">Cash Received (PHP):</label>
                        <input type="number" id="cashAmount" class="form-input" step="0.01" min="0" placeholder="0.00"
                            oninput="calculateChange()">

                        <!-- Quick cash amount buttons -->
                        <div class="quick-cash-buttons" id="quickCashButtons">
                            <!-- Will be populated dynamically based on order total -->
                        </div>
                    </div>
                </div>

                <div class="payment-summary">
                    <div class="summary-row">
                        <span>Order Total:</span>
                        <span id="orderTotalDisplay">PHP 0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Cash Received:</span>
                        <span id="cashReceivedDisplay">PHP 0.00</span>
                    </div>
                    <div class="summary-row change" id="changeDisplay">
                        <span>💰 Change:</span>
                        <span id="changeAmount">PHP 0.00</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="modal-btn modal-btn-cancel" onclick="hidePaymentModal()">Cancel</button>
                    <button class="modal-btn modal-btn-confirm" id="confirmPaymentBtn" onclick="confirmPayment()"
                        disabled>
                        <span id="confirmBtnText">Process Payment</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Maya Payment Verification Modal -->
    <div class="modal-overlay" id="mayaVerifyModal" style="display: none;">
        <div class="modal" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Verify Maya Payment</h3>
                <p>Order #<span id="verifyOrderNumber"></span></p>
            </div>
            <div class="modal-content">
                <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <strong>Amount to Verify:</strong>
                    <div style="font-size: 1.5rem; color: #1976d2; font-weight: bold;">
                        PHP <span id="verifyAmount">0.00</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Maya Reference Number from Receipt:</label>
                    <input type="text" id="mayaReferenceInput" class="form-input"
                        placeholder="Enter ref no. from customer's receipt"
                        style="font-family: monospace; font-size: 1.1rem;">
                    <small style="color: #666;">Ask customer to show their Maya digital receipt</small>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button onclick="hideMayaVerifyModal()"
                        style="flex: 1; background: #6c757d; color: white; padding: 12px; border: none; border-radius: 6px; cursor: pointer;">
                        Cancel
                    </button>
                    <button onclick="submitMayaVerification()"
                        style="flex: 2; background: #28a745; color: white; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        Verify Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal-overlay" id="editOrderModal">
        <div class="edit-modal">
            <div class="edit-modal-header">
                <div class="edit-modal-title">📝 Edit Order Panel</div>
                <div class="edit-modal-subtitle" id="editModalSubtitle">Modify items and quantities</div>
                <button class="edit-modal-close" onclick="closeEditOrderModal()">✕</button>
            </div>

            <div class="edit-modal-content">
                <div class="edit-order-container">
                    <!-- Current Order Items -->
                    <div class="edit-order-section">
                        <div class="edit-section-title">🛒 Order Details</div>
                        <div class="order-items-list" id="editOrderItemsList">
                            <!-- Order items will be populated here -->
                        </div>
                    </div>

                    <!-- Available Menu Items -->
                    <div class="edit-order-section">
                        <div class="edit-section-title">📋 Menu Items</div>
                        <input type="text" class="menu-search" placeholder="🔍 Search menu items..." id="editMenuSearch"
                            oninput="filterEditMenuItems()">
                        <div class="menu-items-list" id="editMenuItemsList">
                            <!-- Menu items will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="edit-modal-footer">
                <div class="edit-total-display" id="editTotalDisplay">Total: PHP 0.00</div>
                <div class="edit-modal-actions">
                    <button class="edit-modal-btn edit-modal-btn-cancel" onclick="closeEditOrderModal()">Cancel</button>
                    <button class="edit-modal-btn edit-modal-btn-save" onclick="saveEditOrderChanges()">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal">
            <div class="modal-header" id="modalHeader">
                <div class="modal-icon" id="modalIcon">⚠️</div>
                <h2 class="modal-title" id="modalTitle">Confirm Action</h2>
                <p class="modal-subtitle" id="modalSubtitle">Please confirm your action</p>
            </div>

            <div class="modal-content">
                <div class="modal-message" id="modalMessage">Are you sure you want to perform this action?</div>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn-cancel" onclick="hideModal()">Cancel</button>
                    <button class="modal-btn modal-btn-confirm" id="confirmBtn"
                        onclick="confirmAction()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Printer Status Indicator -->
    <div class="printer-status" id="printerStatus"></div>

    <script>
        let verifyingOrderId = null;
        let currentAction = null;
        let currentOrderId = null;
        let currentAmount = 0;
        let currentOrderNumber = '';
        let orderTotal = 0;
        let cashReceived = 0;
        let changeAmount = 0;
        let processingOrders = new Map();
        let autoReloadInterval = null;
        let isProcessingPayment = false;
        // Edit Order Modal Variables
        let editOrderData = null;
        let editMenuItems = [
            { id: 1, name: 'Cappuccino', price: 120.00, category: 'Coffee' },
            { id: 2, name: 'Espresso', price: 90.00, category: 'Coffee' },
            { id: 3, name: 'Latte', price: 130.00, category: 'Coffee' },
            { id: 4, name: 'Americano', price: 100.00, category: 'Coffee' },
            { id: 5, name: 'Iced Coffee', price: 110.00, category: 'Coffee' },
            { id: 6, name: 'Blueberry Muffin', price: 85.00, category: 'Pastry' },
            { id: 7, name: 'Chocolate Croissant', price: 95.00, category: 'Pastry' },
            { id: 8, name: 'Caesar Salad', price: 180.00, category: 'Salad' },
            { id: 9, name: 'Greek Salad', price: 165.00, category: 'Salad' },
            { id: 10, name: 'Grilled Sandwich', price: 150.00, category: 'Sandwich' },
            { id: 11, name: 'Chicken Wrap', price: 175.00, category: 'Sandwich' },
            { id: 12, name: 'Fruit Smoothie', price: 140.00, category: 'Beverage' },
            { id: 13, name: 'Hot Chocolate', price: 115.00, category: 'Beverage' },
            { id: 14, name: 'Green Tea', price: 80.00, category: 'Tea' },
            { id: 15, name: 'Earl Grey Tea', price: 85.00, category: 'Tea' }
        ];

        window.Laravel = {
            csrfToken: '<?php echo e(csrf_token()); ?>'
        };

        function debugLog(message, data = null) {
            console.log(`[Cashier Debug] ${message}`, data);
        }

        function getCSRFToken() {
            // Try multiple methods to get CSRF token
            let token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                return token.getAttribute('content');
            }
            // Fallback: try to get from Laravel's global
            if (window.Laravel && window.Laravel.csrfToken) {
                return window.Laravel.csrfToken;
            }
            // Another fallback: try from input field
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) {
                return tokenInput.value;
            }
            console.error('CSRF token not found in page');
            return null;
        }
        //Logout Functions
        function logout() {
            document.getElementById('logoutModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
            document.body.style.overflow = '';
        }
        async function confirmLogout() {
            try {
                await fetchWithErrorHandling('/logout', { method: 'POST' });
                window.location.href = '/';
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = '/';
            }
        }

        // Enhanced fetchWithErrorHandling
        async function fetchWithErrorHandling(url, options = {}) {
            const csrfToken = getCSRFToken();
            if (!csrfToken) {
                throw new Error('CSRF token not available');
            }
            const defaultOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin', // Include cookies
                timeout: 30000 // Increased timeout for payment processing
            };
            const finalOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            };
            debugLog(`Making request to: ${url}`, finalOptions);
            try {
                const timeoutPromise = new Promise((_, reject) => {
                    setTimeout(() => reject(new Error('Request timeout')), finalOptions.timeout);
                });
                const fetchPromise = fetch(url, finalOptions);
                const response = await Promise.race([fetchPromise, timeoutPromise]);
                debugLog(`Response status: ${response.status}`, {
                    ok: response.ok,
                    statusText: response.statusText
                });
                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                        if (errorData.errors) {
                            errorMessage += ' - ' + JSON.stringify(errorData.errors);
                        }
                    } catch (e) {

                    }
                    throw new Error(errorMessage);
                }
                const data = await response.json();
                debugLog('Response data:', data);
                return data;
            } catch (error) {
                debugLog('Fetch error:', error);
                throw error;
            }
        }
        // Edit Order Functions
        function editOrder(orderId) {
            currentAction = 'edit';
            currentOrderId = orderId;
            // Get the order data from the order card
            const orderCard = document.getElementById(`order-${orderId}`);
            if (!orderCard) {
                showErrorMessage('Order not found');
                return;
            }
            // Extract order data from the card
            const orderData = extractOrderDataFromCard(orderCard);
            if (!orderData) {
                showErrorMessage('Could not extract order data');
                return;
            }
            // Open the edit modal with the order data
            openEditOrderModal(orderData);
        }

        function extractOrderDataFromCard(orderCard) {
            try {
                const orderNumber = orderCard.querySelector('.order-number').textContent.replace('#', '');
                const orderItems = [];
                // Extract items from the order card
                const itemElements = orderCard.querySelectorAll('.order-item');
                itemElements.forEach((itemElement, index) => {
                    const itemText = itemElement.querySelector('.item-name').textContent;
                    const priceText = itemElement.querySelector('.item-price').textContent;
                    // Parse item name and quantity
                    const match = itemText.match(/^(.+?)\s+x(\d+)$/);
                    let itemName, quantity;
                    if (match) {
                        itemName = match[1].trim();
                        quantity = parseInt(match[2]);
                    } else {
                        itemName = itemText.trim();
                        quantity = 1;
                    }
                    // Parse price
                    const totalPrice = parseFloat(priceText.replace('PHP ', '').replace(',', ''));
                    const unitPrice = totalPrice / quantity;
                    orderItems.push({
                        id: index + 1, // Use index as temporary ID
                        name: itemName,
                        price: unitPrice,
                        quantity: quantity,
                        originalName: itemText
                    });
                });
                return {
                    id: parseInt(orderCard.getAttribute('data-order-id')),
                    orderNumber: orderNumber,
                    items: orderItems
                };
            } catch (error) {
                console.error('Error extracting order data:', error);
                return null;
            }
        }

        function openEditOrderModal(orderData) {
            editOrderData = JSON.parse(JSON.stringify(orderData)); // Deep clone

            const modal = document.getElementById('editOrderModal');
            const subtitle = document.getElementById('editModalSubtitle');
            if (subtitle) {
                subtitle.textContent = `Order #${orderData.orderNumber} - Modify items and quantities`;
            }
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            populateEditOrderItems();
            populateEditMenuItems();
            updateEditTotal();
        }

        function closeEditOrderModal() {
            const modal = document.getElementById('editOrderModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
            editOrderData = null;
        }

        function printReceipt(orderId) {
            debugLog('printReceipt called with orderId:', orderId);
            if (!orderId) {
                console.error('printReceipt: No orderId provided');
                showPrinterStatus('❌ Cannot print: No order ID', 'error');
                return;
            }

            // Use the clean PHP endpoint
            const receiptUrl = `${window.location.origin}/thermer-receipt.php?id=${orderId}`;
            const thermerUrl = `my.bluetoothprint.scheme://${receiptUrl}`;

            debugLog('Thermer URL:', thermerUrl);

            try {
                // Create a temporary link element for Thermer
                const link = document.createElement('a');
                link.href = thermerUrl;
                link.style.display = 'none';
                document.body.appendChild(link);

                // Click the link to trigger Thermer
                link.click();

                // Clean up
                document.body.removeChild(link);

                showPrinterStatus('Receipt sent to Thermer app for printing', 'success');
                debugLog('Successfully triggered Thermer printing');

            } catch (error) {
                console.error('Error triggering Thermer printing:', error);
                showPrinterStatus('❌ Failed to trigger Thermer printing', 'error');
            }
        }

        function populateEditOrderItems() {
            const container = document.getElementById('editOrderItemsList');

            if (!editOrderData || editOrderData.items.length === 0) {
                container.innerHTML = `
                    <div class="empty-order-state">
                        <div class="empty-order-icon">🍽️</div>
                        <p>No items in order</p>
                        <small>Add items from the menu</small>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';

            editOrderData.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'edit-order-item';
                itemElement.innerHTML = `
                    <div class="edit-item-info">
                        <div class="edit-item-name">${item.name}</div>
                        <div class="edit-item-price">PHP ${item.price.toFixed(2)} each</div>
                    </div>
                    
                    <div class="edit-item-controls">
                        <div class="edit-quantity-controls">
                            <button class="edit-quantity-btn" onclick="decreaseEditQuantity(${item.id})" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                            <span class="edit-quantity-display">${item.quantity}</span>
                            <button class="edit-quantity-btn" onclick="increaseEditQuantity(${item.id})">+</button>
                        </div>
                        
                        <button class="edit-item-remove" onclick="removeEditItem(${item.id})" title="Remove item">
                            🗑️
                        </button>
                    </div>
                `;

                container.appendChild(itemElement);
            });
        }

        function populateEditMenuItems() {
            const container = document.getElementById('editMenuItemsList');
            container.innerHTML = '';

            editMenuItems.forEach(item => {
                const isInOrder = editOrderData.items.some(orderItem => orderItem.name.toLowerCase() === item.name.toLowerCase());
                const itemElement = document.createElement('div');
                itemElement.className = 'edit-menu-item';
                itemElement.innerHTML = `
                    <div class="edit-menu-item-info">
                        <div class="edit-menu-item-name">${item.name}</div>
                        <div class="edit-menu-item-price">PHP ${item.price.toFixed(2)}</div>
                    </div>
                    
                    <button class="edit-add-btn" onclick="addEditItemToOrder(${item.id})" ${isInOrder ? 'disabled' : ''}>
                        ${isInOrder ? 'Added' : 'Add'}
                    </button>
                `;
                container.appendChild(itemElement);
            });
        }

        function filterEditMenuItems() {
            const searchTerm = document.getElementById('editMenuSearch').value.toLowerCase();
            const menuItemsContainer = document.getElementById('editMenuItemsList');
            const menuItemElements = menuItemsContainer.querySelectorAll('.edit-menu-item');

            menuItemElements.forEach(element => {
                const itemName = element.querySelector('.edit-menu-item-name').textContent.toLowerCase();
                if (itemName.includes(searchTerm)) {
                    element.style.display = 'flex';
                } else {
                    element.style.display = 'none';
                }
            });
        }

        function addEditItemToOrder(itemId) {
            const menuItem = editMenuItems.find(item => item.id === itemId);
            if (!menuItem) return;
            const existingItem = editOrderData.items.find(item => item.name.toLowerCase() === menuItem.name.toLowerCase());
            if (existingItem) {
                existingItem.quantity++;
            } else {
                const newId = Math.max(...editOrderData.items.map(item => item.id), 0) + 1;
                editOrderData.items.push({
                    id: newId,
                    name: menuItem.name,
                    price: menuItem.price,
                    quantity: 1
                });
            }

            populateEditOrderItems();
            populateEditMenuItems();
            updateEditTotal();
            showEditNotification(`${menuItem.name} added to order`, 'success');
        }

        function removeEditItem(itemId) {
            const itemIndex = editOrderData.items.findIndex(item => item.id === itemId);
            if (itemIndex > -1) {
                const removedItem = editOrderData.items[itemIndex];
                editOrderData.items.splice(itemIndex, 1);
                populateEditOrderItems();
                populateEditMenuItems();
                updateEditTotal();
                showEditNotification(`${removedItem.name} removed from order`, 'success');
            }
        }

        function increaseEditQuantity(itemId) {
            const item = editOrderData.items.find(item => item.id === itemId);
            if (item) {
                item.quantity++;
                populateEditOrderItems();
                updateEditTotal();
                showEditNotification(`${item.name} quantity increased`, 'success');
            }
        }

        function decreaseEditQuantity(itemId) {
            const item = editOrderData.items.find(item => item.id === itemId);
            if (item && item.quantity > 1) {
                item.quantity--;
                populateEditOrderItems();
                updateEditTotal();
                showEditNotification(`${item.name} quantity decreased`, 'success');
            }
        }

        function updateEditTotal() {
            const total = editOrderData.items.reduce((sum, item) => {
                return sum + (item.price * item.quantity);
            }, 0);

            document.getElementById('editTotalDisplay').textContent = `Total: PHP ${total.toFixed(2)}`;
        }

        async function saveEditOrderChanges() {
            if (!editOrderData) {
                showErrorMessage('No order data to save');
                return;
            }

            try {
                const requestData = {
                    order_id: editOrderData.id,
                    items: editOrderData.items.map(item => ({
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        total_price: item.price * item.quantity
                    }))
                };
                debugLog('Saving order changes:', requestData);
                const data = await fetchWithErrorHandling('/cashier/update-order', {
                    method: 'POST',
                    body: JSON.stringify(requestData)
                });
                if (data.success) {
                    showEditNotification('Order updated successfully!', 'success');
                    // Update the order card in the pending panel
                    updateOrderCardAfterEdit(editOrderData);

                    setTimeout(() => {
                        closeEditOrderModal();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to update order');
                }
            } catch (error) {
                console.error('Error saving order changes:', error);
                showErrorMessage('Failed to save changes: ' + error.message);
            }
        }

        function updateOrderCardAfterEdit(orderData) {
            const orderCard = document.getElementById(`order-${orderData.id}`);
            if (!orderCard) return;
            // Recalculate total
            const newTotal = orderData.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            // Update items section
            const itemsContainer = orderCard.querySelector('.order-items');
            if (itemsContainer) {
                itemsContainer.innerHTML = '';
                orderData.items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'order-item';
                    itemElement.innerHTML = `
                       <span class="item-name">${item.name} x${item.quantity}</span>
                       <span class="item-price">PHP ${(item.price * item.quantity).toFixed(2)}</span>
                   `;
                    itemsContainer.appendChild(itemElement);
                });
            }
            // Update total
            const totalElement = orderCard.querySelector('.total-amount');
            if (totalElement) {
                totalElement.textContent = `PHP ${newTotal.toFixed(2)}`;
            }
            // Update the onclick handlers with new total
            const acceptBtn = orderCard.querySelector('.btn-accept');
            if (acceptBtn) {
                acceptBtn.setAttribute('onclick', `acceptOrder(${orderData.id}, ${newTotal}, '${orderData.orderNumber}', 0)`);
            }
            // Add visual feedback
            orderCard.style.border = '3px solid #28a745';
            setTimeout(() => {
                orderCard.style.border = '3px solid #d4c4a8';
            }, 2000);
        }

        function showEditNotification(message, type = 'success') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.edit-notification');
            existingNotifications.forEach(notification => notification.remove());
            const notification = document.createElement('div');
            notification.className = `edit-notification notification ${type}`;
            notification.style.cssText = `
               position: fixed;
               top: 20px;
               right: 20px;
               padding: 15px 20px;
               border-radius: 8px;
               color: white;
               font-weight: bold;
               z-index: 2000;
               animation: slideInNotification 0.3s ease-out;
               background: ${type === 'success' ? '#28a745' : '#dc3545'};
           `;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Calculate total amount correctly from order items
        function calculateOrderTotal(order) {
            let total = 0;
            // Always calculate from items first (most reliable)
            if (order.order_items && Array.isArray(order.order_items)) {
                order.order_items.forEach(item => {

                    const quantity = parseInt(item.quantity) || 1;
                    // Get unit price (prefer unit_price, then price, then total_price divided by quantity)
                    let unitPrice = 0;
                    if (item.unit_price) {
                        unitPrice = parseFloat(item.unit_price);
                    } else if (item.price && !item.total_price) {
                        unitPrice = parseFloat(item.price);
                    } else if (item.total_price) {
                        unitPrice = parseFloat(item.total_price) / quantity;
                    }
                    // Calculate item total
                    const itemTotal = unitPrice * quantity;
                    if (!isNaN(itemTotal)) {
                        total += itemTotal;
                    }
                    debugLog(`Item calculation:`, {
                        name: item.name,
                        quantity: quantity,
                        unitPrice: unitPrice,
                        itemTotal: itemTotal,
                        runningTotal: total
                    });
                });
            }
            // Only fall back to order.total_amount if no items or total is 0
            if (total <= 0 && order.total_amount) {
                total = parseFloat(order.total_amount);
                debugLog('Using fallback total_amount:', total);
            }
            debugLog('Final calculated order total', {
                originalTotal: order.total_amount,
                calculatedFromItems: total,
                orderItems: order.order_items
            });
            return total;
        }
        // Page visibility API to pause/resume auto-reload when tab is not visible
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopAutoReload();
                debugLog('Page hidden: Auto-reload paused');
            } else {
                // Auto-reload can resume, but don't trigger auto-refresh if it's unnecessary
                debugLog('Page visible: Auto-reload resumed');

                // Refresh immediately when the user comes back, but ensure print isn't triggered
                setTimeout(() => {
                    if (!document.querySelector('#paymentModal.show, #confirmModal.show, #editOrderModal.show')) {
                        autoRefreshOrders();
                    }
                }, 500); // Small delay to ensure all modals are checked properly
            }
        });
        // Auto-refresh function with better error handling
        function autoRefreshOrders() {
            const paymentModal = document.getElementById('paymentModal');
            const confirmModal = document.getElementById('confirmModal');
            const editModal = document.getElementById('editOrderModal');
            // Skip refreshing if modals are open to avoid triggering printing
            if ((paymentModal && paymentModal.classList.contains('show')) ||
                (confirmModal && confirmModal.classList.contains('show')) ||
                (editModal && editModal.classList.contains('show'))) {
                debugLog('Skipping auto-refresh: Modal is open');
                return;
            }
            debugLog('Auto-refreshing orders...');
            fetchWithErrorHandling('/cashier/refresh', { method: 'GET' })
                .then(data => {
                    if (data && data.success === true && Array.isArray(data.orders)) {
                        const currentPendingOrders = Array.from(document.querySelectorAll('.order-card')).map(card =>
                            parseInt(card.getAttribute('data-order-id'))
                        );
                        updateOrdersDisplay(data.orders);
                        const newPendingOrders = Array.from(document.querySelectorAll('.order-card')).map(card =>
                            parseInt(card.getAttribute('data-order-id'))
                        );
                        debugLog('Orders auto-refreshed successfully', {
                            serverTotal: data.orders.length,
                            beforePending: currentPendingOrders.length,
                            afterPending: newPendingOrders.length,
                            processingCount: processingOrders.size
                        });
                        showAutoRefreshIndicator();
                    } else {
                        debugLog('Auto-refresh returned invalid data structure', data);
                        showAutoRefreshError('Invalid data received');
                    }
                })
                .catch(error => {
                    console.error('Error auto-refreshing orders:', error);
                    debugLog('Auto-refresh failed:', error.message);
                    showAutoRefreshError(error.message);
                });
        }

        // Listen for Maya reference updates via Pusher
        document.addEventListener('DOMContentLoaded', function () {
            const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
            const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content;

            if (pusherKey) {
                const pusher = new Pusher(pusherKey, {
                    cluster: pusherCluster,
                    encrypted: true
                });

                const channel = pusher.subscribe('orders');

                // Listen for Maya reference received
                channel.bind('maya-reference-received', function (data) {
                    console.log('Maya reference received:', data);

                    // Update the order card with the reference
                    updateOrderCardWithMayaReference(data);

                    // Show notification
                    showMayaReferenceNotification(data);

                    // Play sound
                    playNotificationSound();
                });

                console.log('Maya real-time notifications enabled');
            }
        });

        function updateOrderCardWithMayaReference(data) {
            const orderCard = document.getElementById(`order-${data.order_id}`);
            if (!orderCard) return;

            // Find the Maya waiting section
            const waitingBox = orderCard.querySelector('.maya-waiting-box');
            if (!waitingBox) return;

            // Replace with reference received section
            waitingBox.innerHTML = `
        <div class="maya-reference-box" style="background: #c8e6c9; border: 2px solid #388e3c; border-radius: 8px; padding: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span style="font-size: 20px;">✅</span>
                <strong style="color: #2e7d32;">Payment Received!</strong>
            </div>
            
            <div style="background: white; padding: 10px; border-radius: 6px; font-family: 'Courier New', monospace; text-align: center; margin-bottom: 10px;">
                <div style="font-size: 0.75rem; color: #666; margin-bottom: 4px;">Reference Number:</div>
                <div style="font-size: 1.2rem; font-weight: bold; color: #1976d2; letter-spacing: 1px;">
                    ${data.maya_reference}
                </div>
            </div>
            
            <div style="font-size: 0.85rem; color: #555; text-align: center; margin-bottom: 10px;">
                💡 Ask customer to show their Maya receipt<br>
                <strong>Verify the reference number matches</strong>
            </div>
            
            <button class="btn btn-confirm-maya" 
                    onclick="quickConfirmMaya(${data.order_id}, '${data.order_number}', '${data.maya_reference}')"
                    style="width: 100%; background: #2e7d32; color: white; padding: 12px; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer;">
                ✅ Confirm Payment Match
            </button>
        </div>
    `;

            // Add visual highlight
            waitingBox.style.animation = 'highlightGreen 2s ease-in-out';
        }

        function showMayaReferenceNotification(data) {
            const notification = document.createElement('div');
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        min-width: 320px;
        animation: slideInFromRight 0.3s ease-out;
    `;

            notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 32px;">💳</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 16px; margin-bottom: 4px;">
                    Maya Payment Detected!
                </div>
                <div style="opacity: 0.95; font-size: 14px;">
                    Order #${data.order_number}
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 6px; border-radius: 4px; margin-top: 6px; font-family: monospace; font-size: 13px;">
                    Ref: ${data.maya_reference}
                </div>
            </div>
        </div>
    `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transition = 'all 0.3s ease-out';
                notification.style.transform = 'translateX(400px)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Quick confirm function
        async function quickConfirmMaya(orderId, orderNumber, mayaReference) {
            try {
                const response = await fetchWithErrorHandling('/maya/quick-confirm', {
                    method: 'POST',
                    body: JSON.stringify({
                        order_id: orderId
                    })
                });

                if (response.success) {
                    showSuccessMessage(`Order #${orderNumber} confirmed! Ref: ${mayaReference}`);

                    // Remove from pending
                    removeOrderFromPending(orderId);

                    // Print receipt
                    printReceipt(orderId);

                    // Refresh after 1 second
                    setTimeout(autoRefreshOrders, 1000);
                } else {
                    throw new Error(response.message || 'Confirmation failed');
                }
            } catch (error) {
                showErrorMessage('Failed to confirm: ' + error.message);
            }
        }

        // Add error indicator for failed auto-refresh
        function showAutoRefreshError(message) {
            const indicator = document.createElement('div');
            indicator.style.cssText = `
               position: fixed;
               top: 10px;
               right: 10px;
               background: rgba(220, 53, 69, 0.9);
               color: white;
               padding: 8px 15px;
               border-radius: 20px;
               font-size: 0.8rem;
               z-index: 1000;
               opacity: 0;
               transition: opacity 0.3s ease;
               pointer-events: none;
           `;
            indicator.innerHTML = `⚠️ Refresh Failed`;
            document.body.appendChild(indicator);
            setTimeout(() => {
                indicator.style.opacity = '1';
            }, 100);
            setTimeout(() => {
                indicator.style.opacity = '0';
                setTimeout(() => {
                    if (indicator.parentElement) {
                        indicator.remove();
                    }
                }, 300);
            }, 3000);
        }

        // Show a subtle indicator that auto-refresh happened
        function showAutoRefreshIndicator() {
            const indicator = document.createElement('div');
            indicator.style.cssText = `
               position: fixed;
               top: 10px;
               right: 10px;
               background: rgba(40, 167, 69, 0.9);
               color: white;
               padding: 8px 15px;
               border-radius: 20px;
               font-size: 0.8rem;
               z-index: 1000;
               opacity: 0;
               transition: opacity 0.3s ease;
               pointer-events: none;
           `;
            indicator.innerHTML = '🔄 Updated';
            document.body.appendChild(indicator);
            setTimeout(() => {
                indicator.style.opacity = '1';
            }, 100);
            setTimeout(() => {
                indicator.style.opacity = '0';
                setTimeout(() => {
                    if (indicator.parentElement) {
                        indicator.remove();
                    }
                }, 300);
            }, 2000);
        }

        // Start auto-reload timer
        function startAutoReload() {
            if (autoReloadInterval) {
                clearInterval(autoReloadInterval);
            }

            autoReloadInterval = setInterval(autoRefreshOrders, 25000);
            debugLog('Auto-reload started: every 25 seconds');
        }

        // Stop auto-reload timer
        function stopAutoReload() {
            if (autoReloadInterval) {
                clearInterval(autoReloadInterval);
                autoReloadInterval = null;
                debugLog('Auto-reload stopped');
            }
        }

        // ENHANCED: Auto-refresh with better order tracking - PREVENTS RE-ADDING PROCESSED ORDERS
        function updateOrdersDisplay(orders) {
            const container = document.getElementById('ordersContainer');
            const emptyState = document.getElementById('emptyState');

            if (!container) return;

            if (!orders || !Array.isArray(orders)) {
                debugLog('Invalid orders data received, skipping update', orders);
                return;
            }
            // Handle empty orders response
            if (orders.length === 0) {
                const existingOrders = container.querySelectorAll('.order-card');
                if (existingOrders.length === 0) {
                    container.innerHTML = emptyState ? emptyState.outerHTML : `
                       <div class="empty-state" id="emptyState">
                           <div class="empty-state-icon">📭</div>
                           <p>No pending cash orders</p>
                           <small>Orders will appear here when customers place cash orders</small>
                           <div style="margin-top: 15px; font-size: 0.8rem; color: #666; display: flex; align-items: center; justify-content: center; gap: 5px;">
                               <span>🔄</span>
                               <span>Auto-updating every 25 seconds</span>
                           </div>
                       </div>
                   `;
                }
                return;
            }

            const currentOrderIds = Array.from(container.querySelectorAll('.order-card')).map(card => parseInt(card.getAttribute('data-order-id')));
            const newPendingOrderIds = orders.filter(order => order.payment_status === 'pending' || order.payment_status === null).map(order => order.id);
            const processingOrderIds = Array.from(processingOrders.keys());
            debugLog('Order comparison during auto-refresh', {
                currentOrderIds,
                newPendingOrderIds,
                processingOrderIds,
                ordersReceived: orders.length
            });

            currentOrderIds.forEach(orderId => {
                if (!newPendingOrderIds.includes(orderId) && !processingOrderIds.includes(orderId)) {
                    const card = document.getElementById(`order-${orderId}`);
                    if (card) {
                        debugLog(`Auto-refresh removing order ${orderId} - confirmed not pending and not processing`);
                        card.remove();
                    }
                } else if (processingOrderIds.includes(orderId)) {
                    // This order is being processed - REMOVE IT from pending if it exists
                    const card = document.getElementById(`order-${orderId}`);
                    if (card) {
                        debugLog(`REMOVING processed order ${orderId} from pending panel - should only be in processing`);
                        card.remove();
                    }
                }
            });
            // Add new pending orders (but NEVER add ones we're already processing)
            orders.forEach(order => {
                if (order.payment_status === 'pending' &&
                    !document.getElementById(`order-${order.id}`) &&
                    !processingOrders.has(order.id)) { // CRITICAL: Don't add if already processing

                    debugLog(`Adding new order ${order.id}`);
                    container.appendChild(createOrderCard(order));

                    const emptyStateElement = container.querySelector('.empty-state');
                    if (emptyStateElement) {
                        emptyStateElement.remove();
                    }
                } else if (processingOrders.has(order.id)) {
                    debugLog(`SKIPPING order ${order.id} - already in processing, should not be in pending`);
                }
            });
            // Check if we need to show empty state
            setTimeout(() => {
                const remainingCards = container.querySelectorAll('.order-card');
                if (remainingCards.length === 0) {
                    container.innerHTML = emptyState ? emptyState.outerHTML : `
                       <div class="empty-state" id="emptyState">
                           <div class="empty-state-icon">📭</div>
                           <p>No pending cash orders</p>
                           <small>Orders will appear here when customers place cash orders</small>
                           <div style="margin-top: 15px; font-size: 0.8rem; color: #666; display: flex; align-items: center; justify-content: center; gap: 5px;">
                               <span>🔄</span>
                               <span>Auto-updating every 25 seconds</span>
                           </div>
                       </div>
                   `;
                }
            }, 100);
        }


        // FIXED: Create order card with correct total calculation and Maya reference display
        function createOrderCard(order) {
            const orderCard = document.createElement('div');
            orderCard.className = 'order-card';
            orderCard.id = `order-${order.id}`;
            orderCard.setAttribute('data-order-id', order.id);

            let itemsHtml = '';
            let calculatedTotal = 0;

            // Handle both 'order_items' and 'items' for compatibility
            const orderItems = order.order_items || order.items || [];

            if (orderItems && Array.isArray(orderItems)) {
                orderItems.forEach(item => {
                    let itemName = item.name || 'Custom Item';
                    itemName = itemName.replace(/\s*x\d+\s*$/, '').trim();

                    const quantity = parseInt(item.quantity) || 1;

                    // Get unit price properly
                    let unitPrice = 0;
                    if (item.unit_price) {
                        unitPrice = parseFloat(item.unit_price);
                    } else if (item.price && !item.total_price) {
                        unitPrice = parseFloat(item.price);
                    } else if (item.total_price) {
                        unitPrice = parseFloat(item.total_price) / quantity;
                    }

                    // Calculate total for this item
                    const itemTotal = unitPrice * quantity;
                    calculatedTotal += itemTotal;

                    debugLog(`Item in createOrderCard:`, {
                        name: itemName,
                        quantity: quantity,
                        unitPrice: unitPrice,
                        itemTotal: itemTotal,
                        calculatedTotal: calculatedTotal
                    });

                    itemsHtml += `
                <div class="order-item">
                    <span class="item-name">${itemName} x${quantity}</span>
                    <span class="item-price">PHP ${itemTotal.toFixed(2)}</span>
                </div>
            `;
                });
            }

            // Use the calculated total, but fallback to order.total or order.total_amount
            const totalAmount = calculatedTotal > 0 ? calculatedTotal : (parseFloat(order.total || order.total_amount || 0));

            debugLog('Order total calculation in createOrderCard:', {
                orderId: order.id,
                orderTotal: order.total,
                orderTotalAmount: order.total_amount,
                calculatedFromItems: calculatedTotal,
                finalTotalUsed: totalAmount,
                items: orderItems
            });

            const cashAmount = parseFloat(order.cash_amount || 0);
            let customerPaymentHtml = '';

            // Show payment info for cash orders
            if (order.payment_method === 'cash') {
                const expectedChange = cashAmount > 0 ? (cashAmount - totalAmount) : 0;
                customerPaymentHtml = `
            <div class="customer-payment-info">
                <div style="font-weight: 700; margin-bottom: 8px; color: #1565c0; display: flex; align-items: center; gap: 8px;">
                    💰 Customer's Payment Plan
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label">🏷️ Order Total:</span>
                    <span>PHP ${totalAmount.toFixed(2)}</span>
                </div>
                ${cashAmount > 0 ? `
                    <div class="payment-info-row">
                        <span class="payment-info-label">💵 Will Bring:</span>
                        <span>PHP ${cashAmount.toFixed(2)}</span>
                    </div>
                    ${expectedChange > 0 ? `
                        <div class="payment-info-row expected-change">
                            <span class="payment-info-label">💸 Expected Change:</span>
                            <span>PHP ${expectedChange.toFixed(2)}</span>
                        </div>
                    ` : ''}
                ` : `
                    <div class="payment-info-row">
                        <span class="payment-info-label">💵 Will Bring:</span>
                        <span style="color: #666; font-style: italic;">Amount to be determined</span>
                    </div>
                `}
                <div class="edit-amount-note">
                    💡 You can edit the cash amount during payment processing
                </div>
            </div>
        `;
            } else if (order.payment_method === 'maya') {
                // Show Maya payment info with reference if available
                if (order.maya_reference) {
                    customerPaymentHtml = `
                <div class="maya-payment-section" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: 2px solid #1976d2; border-radius: 10px; padding: 15px; margin: 15px 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 24px;">📱</span>
                        <strong style="color: #1565c0; font-size: 1.1rem;">Maya QR Payment</strong>
                    </div>
                    
                    <div class="maya-reference-box" style="background: #c8e6c9; border: 2px solid #388e3c; border-radius: 8px; padding: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="font-size: 20px;">✅</span>
                            <strong style="color: #2e7d32;">Payment Received!</strong>
                        </div>
                        
                        <div style="background: white; padding: 10px; border-radius: 6px; font-family: 'Courier New', monospace; text-align: center; margin-bottom: 10px;">
                            <div style="font-size: 0.75rem; color: #666; margin-bottom: 4px;">Reference Number:</div>
                            <div style="font-size: 1.2rem; font-weight: bold; color: #1976d2; letter-spacing: 1px;">
                                ${order.maya_reference}
                            </div>
                        </div>
                        
                        <div style="font-size: 0.85rem; color: #555; text-align: center; margin-bottom: 10px;">
                            💡 Ask customer to show their Maya receipt<br>
                            <strong>Verify the reference number matches</strong>
                        </div>
                        
                        <button class="btn btn-confirm-maya" 
                                onclick="quickConfirmMaya(${order.id}, '${order.order_number || order.id}', '${order.maya_reference}')"
                                style="width: 100%; background: #2e7d32; color: white; padding: 12px; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer;">
                            ✅ Confirm Payment Match
                        </button>
                    </div>
                </div>
            `;
                } else {
                    customerPaymentHtml = `
                <div class="maya-payment-section" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: 2px solid #1976d2; border-radius: 10px; padding: 15px; margin: 15px 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 24px;">📱</span>
                        <strong style="color: #1565c0; font-size: 1.1rem;">Maya QR Payment</strong>
                    </div>
                    
                    <div class="maya-waiting-box" style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 12px; text-align: center;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">⏳</div>
                        <div style="font-weight: 600; color: #856404; margin-bottom: 6px;">
                            Waiting for Payment...
                        </div>
                        <div style="font-size: 0.85rem; color: #856404;">
                            Customer is scanning QR code<br>
                            Reference will appear here automatically
                        </div>
                        
                        <div class="loading-dots" style="margin-top: 10px;">
                            <span style="animation: blink 1.4s infinite;">●</span>
                            <span style="animation: blink 1.4s infinite 0.2s;">●</span>
                            <span style="animation: blink 1.4s infinite 0.4s;">●</span>
                        </div>
                    </div>
                </div>
            `;
                }
            }

            const orderNumber = order.order_number || order.id.toString().padStart(4, '0');
            const createdAt = order.created_at ? new Date(order.created_at) : new Date();
            const timeString = createdAt.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            const orderType = order.order_type || 'dine-in';

            orderCard.innerHTML = `
        <div class="order-header">
            <span>Order</span>
            <span class="order-number">#${orderNumber}</span>
        </div>
        <div class="order-time">
            Placed at ${timeString}
            <span class="order-type">${orderType.charAt(0).toUpperCase() + orderType.slice(1)}</span>
        </div>
        <div class="status-badge status-pending">Pending Payment</div>
        <div class="payment-method-badge ${order.payment_method === 'maya' ? 'payment-maya' : 'payment-cash'}">
            ${order.payment_method ? order.payment_method.toUpperCase() : 'CASH'}
        </div>
        
        <div class="order-items">
            ${itemsHtml}
        </div>

        ${customerPaymentHtml}
        
        <div class="order-total">
            <span>Total Amount:</span>
            <span class="total-amount">PHP ${totalAmount.toFixed(2)}</span>
        </div>
        
        <div class="order-actions">
            <button class="btn btn-accept" onclick="acceptOrder(${order.id}, ${totalAmount}, '${orderNumber}', ${cashAmount})">
                ✅ Accept
            </button>
            <button class="btn btn-edit" onclick="editOrder(${order.id})">
                ✏️ Edit
            </button>
            <button class="btn btn-cancel" onclick="cancelOrder(${order.id})">
                ❌ Cancel
            </button>
        </div>
    `;

            return orderCard;
        }

        // Your existing acceptOrder function stays the same
        function acceptOrder(orderId, amount, orderNumber, cashAmount = null) {
            currentOrderId = parseInt(orderId);
            currentAmount = parseFloat(amount) || 0;
            currentOrderNumber = orderNumber || orderId.toString().padStart(4, '0');
            orderTotal = currentAmount;

            // Show payment method selection modal
            showPaymentMethodModal();
        }

        function showPaymentMethodModal() {
            // Create and show payment method selection
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.id = 'paymentMethodModal';
            modal.innerHTML = `
        <div class="modal" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Select Payment Method</h2>
                <p>Order #${currentOrderNumber} - PHP ${orderTotal.toFixed(2)}</p>
            </div>
            <div class="modal-content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">
                    <button onclick="processCashPayment()" style="background: #28a745; color: white; border: none; padding: 20px; border-radius: 8px; cursor: pointer; font-size: 1.1rem;">
                        💵 Cash Payment
                    </button>
                    <button onclick="processMayaPayment()" style="background: #00a8ff; color: white; border: none; padding: 20px; border-radius: 8px; cursor: pointer; font-size: 1.1rem;">
                        📱 Maya QR
                    </button>
                </div>
                <button onclick="hidePaymentMethodModal()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; width: 100%;">
                    Cancel
                </button>
            </div>
        </div>
    `;

            document.body.appendChild(modal);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function generateQuickCashButtons() {
            const container = document.getElementById('quickCashButtons');
            if (!container) return;

            const total = orderTotal;
            const quickAmounts = [
                Math.ceil(total),
                Math.ceil(total / 50) * 50,
                Math.ceil(total / 100) * 100,
                Math.ceil(total / 500) * 500,
                1000,
                500
            ];

            const uniqueAmounts = [...new Set(quickAmounts)]
                .filter(amount => amount >= total)
                .sort((a, b) => a - b)
                .slice(0, 6);
            container.innerHTML = '';
            uniqueAmounts.forEach(amount => {
                const button = document.createElement('button');
                button.className = 'quick-cash-btn';
                button.textContent = `PHP ${amount}`;
                button.onclick = () => setQuickCash(amount);
                container.appendChild(button);
            });
        }

        function setQuickCash(amount) {
            const cashInput = document.getElementById('cashAmount');
            if (cashInput) {
                cashInput.value = amount.toFixed(2);
                calculateChange();
            }
        }

        function calculateChange() {
            const cashInput = document.getElementById('cashAmount');
            if (!cashInput) return;

            cashReceived = parseFloat(cashInput.value) || 0;
            changeAmount = cashReceived - orderTotal;

            updatePaymentDisplays();

            const confirmBtn = document.getElementById('confirmPaymentBtn');
            if (confirmBtn) {
                if (cashReceived >= orderTotal && !isProcessingPayment) {
                    confirmBtn.disabled = false;
                    cashInput.classList.remove('error');
                } else {
                    confirmBtn.disabled = true;
                    if (cashReceived > 0 && cashReceived < orderTotal) {
                        cashInput.classList.add('error');
                    } else {
                        cashInput.classList.remove('error');
                    }
                }
            }
        }

        function updatePaymentDisplays() {
            const cashReceivedDisplay = document.getElementById('cashReceivedDisplay');
            const changeAmountDisplay = document.getElementById('changeAmount');
            const changeDisplay = document.getElementById('changeDisplay');

            if (cashReceivedDisplay) {
                cashReceivedDisplay.textContent = `PHP ${cashReceived.toFixed(2)}`;
            }
            if (changeAmountDisplay) {
                changeAmountDisplay.textContent = `PHP ${Math.max(0, changeAmount).toFixed(2)}`;
            }
            console.log('Change calculation debug:', {
                cashReceived,
                orderTotal,
                changeAmount: cashReceived - orderTotal
            });
            if (changeDisplay) {
                if (changeAmount < 0) {
                    changeDisplay.classList.add('negative');
                    const firstSpan = changeDisplay.querySelector('span:first-child');
                    if (firstSpan) firstSpan.textContent = '⚠️ Insufficient:';
                } else {
                    changeDisplay.classList.remove('negative');
                    const firstSpan = changeDisplay.querySelector('span:first-child');
                    if (firstSpan) firstSpan.textContent = '💰 Change:';
                }
            }
        }

        function hidePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) {
                modal.classList.remove('show');
            }
            document.body.style.overflow = '';

            // Reset payment processing state
            isProcessingPayment = false;
            resetConfirmButton();

            currentOrderId = null;
            currentAmount = 0;
            currentOrderNumber = '';
            orderTotal = 0;
            cashReceived = 0;
            changeAmount = 0;
        }

        // Function to remove order from left panel
        function removeOrderFromPending(orderId) {
            debugLog(`Attempting to remove order ${orderId} from pending panel`);

            // Try both formats: with and without leading zeros
            let orderCard = document.getElementById(`order-${orderId}`);

            // If not found, try with leading zeros (padded to 4 digits)
            if (!orderCard) {
                const paddedId = orderId.toString().padStart(4, '0');
                orderCard = document.getElementById(`order-${paddedId}`);
                debugLog(`Trying padded ID: order-${paddedId}`);
            }

            if (orderCard) {
                debugLog(`Found order card ${orderCard.id}, removing it now`);

                // Add fade out animation
                orderCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                orderCard.style.opacity = '0';
                orderCard.style.transform = 'translateX(-20px)';

                // Remove the element after animation
                setTimeout(() => {
                    orderCard.remove();
                    debugLog(`Order ${orderId} successfully removed from DOM`);

                    // Check if there are any remaining orders
                    const ordersContainer = document.getElementById('ordersContainer');
                    const remainingOrders = ordersContainer.querySelectorAll('.order-card');

                    // If no orders remain, show empty state
                    if (remainingOrders.length === 0) {
                        const emptyStateHtml = `
                           <div class="empty-state" id="emptyState">
                               <div class="empty-state-icon">📭</div>
                               <p>No pending cash orders</p>
                               <small>Orders will appear here when customers place cash orders</small>
                           </div>
                       `;
                        ordersContainer.innerHTML = emptyStateHtml;
                        debugLog('Added empty state after removing last order');
                    }
                }, 300);
            } else {
                debugLog(`ERROR: Order card not found for ID ${orderId}`);
                // Debug what cards actually exist
                const allCards = document.querySelectorAll('.order-card');
                const cardIds = Array.from(allCards).map(card => card.id);
                debugLog('Available order cards:', cardIds);
            }
        }

        // MAIN PAYMENT PROCESSING FUNCTION - FIXED WITH PRINTER INTEGRATION
        async function confirmPayment() {
            debugLog('Confirm payment called', {
                orderId: currentOrderId,
                cashReceived,
                orderTotal,
                changeAmount,
                isProcessingPayment
            });
            // Prevent double-clicking
            if (isProcessingPayment) {
                debugLog('Payment already in progress, ignoring click');
                return;
            }
            if (cashReceived < orderTotal) {
                showErrorMessage('Insufficient cash amount');
                return;
            }
            if (!currentOrderId) {
                showErrorMessage('No order selected');
                return;
            }
            // Set processing state
            isProcessingPayment = true;
            setConfirmButtonLoading(true);
            // Show printer status
            showPrinterStatus('Processing payment and printing receipt...', 'processing');
            const requestData = {
                order_id: parseInt(currentOrderId),
                cash_amount: parseFloat(cashReceived),
                print_receipt: true
            };
            debugLog('Payment request data:', requestData);
            // Calculate the actual change before processing
            const actualChange = cashReceived - orderTotal;
            // Store current order data before processing - THIS IS THE KEY FIX
            const processingOrderId = currentOrderId;
            const processingOrderNumber = currentOrderNumber;
            // Get the order card element BEFORE processing
            const orderCard = document.getElementById(`order-${currentOrderId}`);
            let orderData = null;
            if (orderCard) {
                // Extract order data from the card for processing panel
                orderData = {
                    id: currentOrderId,
                    orderNumber: currentOrderNumber,
                    orderCard: orderCard.cloneNode(true),
                    totalAmount: orderTotal
                };
                debugLog('Order card found and data extracted', {
                    cardId: orderCard.id,
                    orderData: orderData
                });
            } else {
                debugLog('WARNING: Order card not found!', { orderId: currentOrderId });
            }
            // CRITICAL: Remove from pending IMMEDIATELY before API call
            // This prevents auto-refresh from bringing it back
            removeOrderFromPending(processingOrderId);

            hidePaymentModal();
            try {
                const data = await fetchWithErrorHandling('/cashier/accept-order', {
                    method: 'POST',
                    body: JSON.stringify(requestData)
                });
                //if payment sucessful
                if (data.success) {
                    debugLog('About to print receipt for order:', processingOrderId);

                    // Use the clean PHP endpoint
                    const receiptUrl = `${window.location.origin}/thermer-receipt.php?id=${processingOrderId}`;
                    const thermerUrl = `my.bluetoothprint.scheme://${receiptUrl}`;

                    debugLog('Thermer printing with clean endpoint:', thermerUrl);

                    // Create temporary link to trigger Thermer
                    const link = document.createElement('a');
                    link.href = thermerUrl;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    showPrinterStatus('Payment completed! Printing receipt...', 'success');
                    // Move order to processing panel with correct total
                    moveOrderToProcessing(processingOrderId, processingOrderNumber, actualChange, data.receipt_printed, orderData, orderTotal);
                    // Show success message
                    showSuccessMessage(`Order #${processingOrderNumber} payment processed successfully!`);
                    // Log the complete transaction
                    debugLog('Transaction completed', {
                        orderId: processingOrderId,
                        totalAmount: orderTotal,
                        cashReceived: cashReceived,
                        changeGiven: actualChange,
                        receiptPrinted: data.receipt_printed
                    });
                } else {
                    // If payment failed, restore the order to pending
                    if (orderData && orderData.orderCard) {
                        restoreOrderToPending(orderData);
                    }
                    throw new Error(data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Payment processing error:', error);
                // If payment failed and we removed the order, restore it
                if (orderData && orderData.orderCard) {
                    restoreOrderToPending(orderData);
                }
                // Show error message
                showErrorMessage('Payment processing failed: ' + error.message);
                // Show printer error status
                showPrinterStatus('❌ Payment failed: ' + error.message, 'error');
                // Reset processing state
                isProcessingPayment = false;
                setConfirmButtonLoading(false);
            }
        }

        //NEW
        function processCashPayment() {
            hidePaymentMethodModal();
            showPaymentModal(); // Your existing cash payment modal
        }

        function processMayaPayment() {
            hidePaymentMethodModal();
            showMayaQRModal();
        }

        function showMayaQRModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.id = 'mayaQRModal';
            modal.innerHTML = `
        <div class="modal" style="max-width: 300px;">
            <div class="modal-header">
                <h3>Maya QR Payment</h3>
                <p style="font-size: 0.9rem;">Order #${currentOrderNumber} - PHP ${orderTotal.toFixed(2)}</p>
            </div>
            <div class="modal-content" style="text-align: center;">
                <div style="background: white; padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <img src="/assets/maya-qr.png" alt="Maya QR Code" style="width: 150px; height: 150px; border: 2px solid #00a8ff; border-radius: 8px;">
                    <p style="margin-top: 10px; font-weight: bold; color: #00a8ff; font-size: 0.9rem;">Ask customer to scan with Maya app</p>
                </div>
                
                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin: 12px 0; text-align: left; font-size: 0.85rem;">
                    <strong>Instructions:</strong><br>
                    1. Show QR to customer<br>
                    2. Customer scans with Maya<br>
                    3. Customer pays<br>
                    4. Confirm payment below
                </div>
                
                <div style="background: #fff3cd; padding: 12px; border-radius: 6px; margin: 12px 0; font-size: 0.9rem;">
                    <strong>Amount: PHP ${orderTotal.toFixed(2)}</strong><br>
                    <small>Ref: Order #${currentOrderNumber}</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button onclick="hideMayaQRModal()" style="background: #6c757d; color: white; border: none; padding: 12px 15px; border-radius: 6px; cursor: pointer; flex: 1; font-size: 0.9rem;">
                        Cancel
                    </button>
                    <button onclick="confirmMayaPayment()" style="background: #28a745; color: white; border: none; padding: 12px 15px; border-radius: 6px; cursor: pointer; flex: 2; font-weight: bold; font-size: 0.9rem;">
                        Payment Received
                    </button>
                </div>
            </div>
        </div>
    `;

            document.body.appendChild(modal);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function showMayaVerifyModal(orderId, orderNumber, amount) {
            verifyingOrderId = orderId;
            document.getElementById('verifyOrderNumber').textContent = orderNumber;
            document.getElementById('verifyAmount').textContent = parseFloat(amount).toFixed(2);
            document.getElementById('mayaReferenceInput').value = '';
            document.getElementById('mayaVerifyModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Focus on input
            setTimeout(() => {
                document.getElementById('mayaReferenceInput').focus();
            }, 300);
        }

        function hideMayaVerifyModal() {
            document.getElementById('mayaVerifyModal').style.display = 'none';
            document.body.style.overflow = '';
            verifyingOrderId = null;
        }

        async function submitMayaVerification() {
            const referenceNumber = document.getElementById('mayaReferenceInput').value.trim();

            if (!referenceNumber) {
                showErrorMessage('Please enter the Maya reference number');
                return;
            }

            if (!verifyingOrderId) {
                showErrorMessage('No order selected');
                return;
            }

            try {
                const response = await fetchWithErrorHandling('/maya/verify-payment', {
                    method: 'POST',
                    body: JSON.stringify({
                        order_id: verifyingOrderId,
                        reference_number: referenceNumber
                    })
                });

                if (response.success) {
                    hideMayaVerifyModal();
                    showSuccessMessage(`Payment verified! Ref: ${referenceNumber}`);

                    // Remove from pending and trigger confirmation flow
                    removeOrderFromPending(verifyingOrderId);
                    printReceipt(verifyingOrderId);

                    // Refresh orders
                    setTimeout(autoRefreshOrders, 1000);
                } else {
                    throw new Error(response.message || 'Verification failed');
                }
            } catch (error) {
                showErrorMessage('Verification failed: ' + error.message);
            }
        }

        async function confirmMayaPayment() {
            try {
                setMayaButtonLoading(true);

                const requestData = {
                    order_id: parseInt(currentOrderId),
                    cash_amount: parseFloat(orderTotal),
                    payment_method: 'maya',
                    maya_confirmed: true,
                    print_receipt: true
                };

                // Use the same endpoint as cash payments
                const data = await fetchWithErrorHandling('/cashier/accept-order', {
                    method: 'POST',
                    body: JSON.stringify(requestData)
                });

                if (data.success) {
                    hideMayaQRModal();
                    removeOrderFromPending(currentOrderId);
                    printReceipt(currentOrderId);
                    moveOrderToProcessing(currentOrderId, currentOrderNumber, 0, data.receipt_printed, null, orderTotal);
                    showSuccessMessage(`Maya payment confirmed for Order #${currentOrderNumber}!`);
                } else {
                    throw new Error(data.message || 'Failed to confirm payment');
                }
            } catch (error) {
                showErrorMessage('Failed to confirm Maya payment: ' + error.message);
            } finally {
                setMayaButtonLoading(false);
            }
            // To manually verify if auto-verification fails (Maya)
            setTimeout(() => {
                if (currentOrderId) {
                    showMayaVerifyModal(currentOrderId, currentOrderNumber, orderTotal);
                }
            }, 30000); // Show after 30 seconds if no webhook received
        }

        function showPaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (!modal) {
                console.error('Payment modal not found');
                return;
            }

            const header = document.getElementById('paymentModalHeader');
            const orderTotalDisplay = document.getElementById('orderTotalDisplay');
            const cashInput = document.getElementById('cashAmount');

            // Update modal content
            if (orderTotalDisplay) {
                orderTotalDisplay.textContent = `PHP ${orderTotal.toFixed(2)}`;
            }

            // Clear previous input
            if (cashInput) {
                cashInput.value = '';
                cashInput.focus();
            }

            // Generate quick cash buttons
            generateQuickCashButtons();

            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Reset displays
            updatePaymentDisplays();
        }
        function hidePaymentMethodModal() {
            const modal = document.getElementById('paymentMethodModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = '';
            }
        }

        function hideMayaQRModal() {
            const modal = document.getElementById('mayaQRModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = '';
            }
        }

        function setMayaButtonLoading(loading) {
            const confirmBtn = document.querySelector('#mayaQRModal button[onclick="confirmMayaPayment()"]');
            if (confirmBtn) {
                if (loading) {
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Confirming...';
                } else {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Payment Received - Confirm';
                }
            }
        }

        function setConfirmButtonLoading(loading) {
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            const btnText = document.getElementById('confirmBtnText');

            if (!confirmBtn || !btnText) return;

            if (loading) {
                confirmBtn.classList.add('processing');
                confirmBtn.disabled = true;
                btnText.textContent = 'Processing...';
            } else {
                confirmBtn.classList.remove('processing');
                confirmBtn.disabled = cashReceived < orderTotal;
                btnText.textContent = 'Process Payment';
            }
        }

        function resetConfirmButton() {
            setConfirmButtonLoading(false);
        }

        function showPrinterStatus(message, type = 'info') {
            const printerStatus = document.getElementById('printerStatus');
            if (!printerStatus) return;

            printerStatus.textContent = message;
            printerStatus.className = `printer-status show ${type}`;
            // Auto-hide after delay based on type
            const delay = type === 'error' ? 8000 : type === 'warning' ? 6000 : 4000;
            setTimeout(() => {
                printerStatus.classList.remove('show');
            }, delay);
        }

        // NEW: Restore order to pending if payment fails
        function restoreOrderToPending(orderData) {
            const container = document.getElementById('ordersContainer');
            if (!container || !orderData.orderCard) return;

            debugLog('Restoring order to pending after failed payment', orderData);

            // Remove empty state if it exists
            const emptyStateElement = container.querySelector('.empty-state');
            if (emptyStateElement) {
                emptyStateElement.remove();
            }

            // Clone and restore the original order card
            const restoredCard = orderData.orderCard.cloneNode(true);
            restoredCard.id = `order-${orderData.id}`;
            restoredCard.setAttribute('data-order-id', orderData.id);

            container.appendChild(restoredCard);
            debugLog(`Order ${orderData.id} restored to pending after payment failure`);
        }

        // Move order to processing with better tracking
        function moveOrderToProcessing(orderId, orderNumber, change, receiptPrinted, orderData = null, actualOrderTotal = 0) {
            debugLog('Moving order to processing', {
                orderId,
                orderNumber,
                change,
                receiptPrinted,
                hasOrderData: !!orderData,
                actualOrderTotal
            });

            // Store in processing orders map
            processingOrders.set(orderId, {
                orderNumber,
                change: change,
                receiptPrinted,
                startTime: new Date(),
                status: 'preparing',
                orderData: orderData
            });

            // Show in processing panel
            showProcessingOrder(orderId, orderNumber, change, receiptPrinted, orderData, actualOrderTotal);
        }

        // Enhanced: Show processing order with "Move Back to Pending" option
        function showProcessingOrder(orderId, orderNumber, actualChangeGiven, receiptPrinted, orderData = null, actualOrderTotal = 0) {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) {
                console.error('Processing section not found');
                return;
            }

            const displayOrderNumber = orderNumber || orderId.toString().padStart(4, '0');

            // FIX: Calculate the correct total if not provided or if it's 0
            let displayTotal = actualOrderTotal;
            if (displayTotal <= 0 && orderData && orderData.orderCard) {
                // Extract total from the order card's total display
                const totalElement = orderData.orderCard.querySelector('.total-amount');
                if (totalElement) {
                    const totalText = totalElement.textContent.replace('PHP ', '').replace(',', '');
                    displayTotal = parseFloat(totalText) || 0;
                }
            }

            // If still 0, try to get from orderData.totalAmount
            if (displayTotal <= 0 && orderData && orderData.totalAmount) {
                displayTotal = orderData.totalAmount;
            }

            // Final fallback - calculate from order items if available
            if (displayTotal <= 0 && orderData && orderData.orderCard) {
                const itemElements = orderData.orderCard.querySelectorAll('.order-item .item-price');
                itemElements.forEach(element => {
                    const priceText = element.textContent.replace('PHP ', '').replace(',', '');
                    displayTotal += parseFloat(priceText) || 0;
                });
            }

            debugLog('Showing processing order with corrected total', {
                orderId,
                orderNumber: displayOrderNumber,
                actualChangeGiven,
                receiptPrinted,
                hasOrderData: !!orderData,
                originalTotal: actualOrderTotal,
                correctedTotal: displayTotal
            });

            // Check if this is the first processing order
            if (!processingSection.classList.contains('has-order')) {
                processingSection.className = 'processing-section has-order';
                processingSection.innerHTML = '<div class="processing-orders-container"></div>';
            }

            const ordersContainer = processingSection.querySelector('.processing-orders-container');
            if (!ordersContainer) {
                processingSection.innerHTML = '<div class="processing-orders-container"></div>';
            }

            const receiptStatus = receiptPrinted ?
                '✅ Receipt printed successfully!' :
                '⚠️ Order processed (receipt printing failed - please check printer)';

            const startTime = new Date();
            const estimatedTime = new Date(startTime.getTime() + (15 * 60000)); // 15 minutes estimated

            // Extract order items from original order data if available
            let orderItemsHtml = '';
            if (orderData && orderData.orderCard) {
                const originalItems = orderData.orderCard.querySelector('.order-items');
                if (originalItems) {
                    orderItemsHtml = `
                       <div class="processing-order-items" style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin: 15px 0;">
                           <div style="font-weight: 600; margin-bottom: 10px; color: #495057;">📋 Order Items:</div>
                           ${originalItems.innerHTML}
                       </div>
                   `;
                }
            }

            // Create individual order processing card
            const orderProcessingCard = document.createElement('div');
            orderProcessingCard.className = 'processing-order-card';
            orderProcessingCard.id = `processing-order-${orderId}`;
            orderProcessingCard.style.cssText = `
               background: white;
               border-radius: 15px;
               padding: 20px;
               margin-bottom: 20px;
               box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
               border: 2px solid #e9ecef;
               animation: slideInFromLeft 0.5s ease-out;
           `;

            orderProcessingCard.innerHTML = `
               <div class="processing-order-header" style="margin-bottom: 20px;">
                   <div class="processing-order-number" style="font-size: 1.4rem; font-weight: 700; color: #8B4513;">🏷️ Order #${displayOrderNumber}</div>
                   <div class="processing-order-time" style="color: #666; font-size: 0.9rem;">💳 Payment completed at ${startTime.toLocaleTimeString()}</div>
               </div>

               ${orderItemsHtml}
           
               <div class="payment-details-box" style="background: #d4edda; border: 2px solid #c3e6cb; border-radius: 10px; padding: 20px; margin: 20px 0;">
                   <div style="font-size: 1.3rem; font-weight: 700; color: #155724; margin-bottom: 15px; text-align: center;">
                       💰 Payment Completed Successfully!
                   </div>
                   <div class="processing-total-row" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1.1rem;">
                       <span style="color: #155724;">🏷️ Order Total:</span>
                       <span style="font-weight: 700; color: #155724;">PHP ${displayTotal.toFixed(2)}</span>
                   </div>
                   <div class="processing-total-row" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1.1rem;">
                       <span style="color: #155724;">💸 Change Given:</span>
                       <span style="font-weight: 700; color: #155724; font-size: 1.2rem;">PHP ${actualChangeGiven.toFixed(2)}</span>
                   </div>
                   <div style="margin-top: 15px; padding: 12px; background: rgba(21, 87, 36, 0.1); border-radius: 8px; text-align: center; color: #155724; font-weight: 600;">
                       ${receiptStatus}
                   </div>
               </div>

               <div style="background: #fff3cd; border: 2px solid #ffeaa7; border-radius: 10px; padding: 20px; margin: 20px 0;">
                   <div style="font-weight: 600; margin-bottom: 15px; color: #856404; text-align: center; font-size: 1.1rem;">⏱️ Order Status</div>
                   <div style="color: #856404; line-height: 1.8; text-align: center;">
                       <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 10px;">🍳 Currently Preparing</div>
                       <div style="font-size: 1rem;">📅 Estimated completion: ${estimatedTime.toLocaleTimeString()}</div>
                       <div style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">⏰ Average wait time: 15 minutes</div>
                   </div>
               </div>
               
               <div class="processing-actions" style="display: flex; gap: 15px; margin-top: 25px;">
                   <button class="btn-large" style="
                       background: #28a745; 
                       color: white; 
                       border: none; 
                       border-radius: 8px; 
                       cursor: pointer; 
                       padding: 15px 25px; 
                       font-size: 1.1rem; 
                       flex: 1;
                       font-weight: 600;
                       transition: background-color 0.3s ease;
                   " onclick="completeOrder(${orderId})" onmouseover="this.style.backgroundColor='#218838'" onmouseout="this.style.backgroundColor='#28a745'">
                       ✅ Mark as Complete
                   </button>
                   <button class="btn-large" style="
                       background: #6c757d; 
                       color: white; 
                       border: none; 
                       border-radius: 8px; 
                       cursor: pointer; 
                       padding: 15px 25px; 
                       font-size: 1.1rem; 
                       flex: 1;
                       font-weight: 600;
                       transition: background-color 0.3s ease;
                   " onclick="moveBackToPending(${orderId})" onmouseover="this.style.backgroundColor='#5a6268'" onmouseout="this.style.backgroundColor='#6c757d'">
                       ↩️ Move Back to Pending
                   </button>
               </div>
           `;

            // Add to the container
            const container = processingSection.querySelector('.processing-orders-container');
            container.appendChild(orderProcessingCard);

            // Update the header to show count
            updateProcessingHeader();
        }

        // Enhanced: Move order back to pending panel (recreates the order card)
        function moveBackToPending(orderId) {
            const processingOrder = processingOrders.get(orderId);
            if (!processingOrder) {
                showErrorMessage('Order not found in processing');
                return;
            }

            debugLog('Moving order back to pending', { orderId, processingOrder });

            // Remove from processing orders map
            processingOrders.delete(orderId);

            // Remove processing card with animation
            const processingCard = document.getElementById(`processing-order-${orderId}`);
            if (processingCard) {
                processingCard.style.transition = 'all 0.5s ease';
                processingCard.style.transform = 'translateX(-100%)';
                processingCard.style.opacity = '0';

                setTimeout(() => {
                    processingCard.remove();
                    updateProcessingHeader();

                    // If no more processing orders, reset panel
                    const remainingCards = document.querySelectorAll('.processing-order-card');
                    if (remainingCards.length === 0) {
                        resetProcessingPanel();
                    }
                }, 500);
            }

            // Recreate the order in pending panel
            if (processingOrder.orderData && processingOrder.orderData.orderCard) {
                const container = document.getElementById('ordersContainer');

                if (container) {
                    // Remove empty state if it exists
                    const emptyStateElement = container.querySelector('.empty-state');
                    if (emptyStateElement) {
                        emptyStateElement.remove();
                    }

                    // Clone and restore the original order card
                    const restoredCard = processingOrder.orderData.orderCard.cloneNode(true);
                    restoredCard.id = `order-${orderId}`;
                    restoredCard.setAttribute('data-order-id', orderId);

                    // Start with hidden state
                    restoredCard.style.transform = 'translateX(-100%)';
                    restoredCard.style.opacity = '0';
                    restoredCard.style.transition = 'all 0.5s ease';

                    container.appendChild(restoredCard);

                    // Animate in
                    setTimeout(() => {
                        restoredCard.style.transform = 'translateX(0)';
                        restoredCard.style.opacity = '1';
                        debugLog(`Order ${orderId} restored to pending panel`);
                    }, 100);
                }
            }

            // Show success message
            showSuccessMessage(`Order #${processingOrder.orderNumber} moved back to pending`);
        }

        function updateProcessingHeader() {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) return;

            const processingCards = processingSection.querySelectorAll('.processing-order-card');
            const count = processingCards.length;

            // Add or update header
            let header = processingSection.querySelector('.processing-section-header');
            if (!header && count > 0) {
                header = document.createElement('div');
                header.className = 'processing-section-header';
                header.style.cssText = `
                   background: #8B4513;
                   color: white;
                   padding: 15px 20px;
                   border-radius: 10px 10px 0 0;
                   margin-bottom: 20px;
                   text-align: center;
                   font-weight: 700;
                   font-size: 1.2rem;
               `;
                processingSection.insertBefore(header, processingSection.firstChild);
            }

            if (header) {
                header.innerHTML = `🍳 Processing Orders (${count})`;
            }
        }

        // Fixed: Individual order completion
        async function completeOrder(orderId) {
            const processingOrder = processingOrders.get(orderId);
            if (!processingOrder) {
                showErrorMessage('Order not found in processing');
                return;
            }

            try {
                const salesData = {
                    order_id: parseInt(orderId),
                    order_number: processingOrder.orderNumber,
                    completion_time: new Date().toISOString(),
                    change_given: processingOrder.change,
                    receipt_printed: processingOrder.receiptPrinted
                };

                debugLog('Completing order', salesData);

                const data = await fetchWithErrorHandling('/cashier/complete-order', {
                    method: 'POST',
                    body: JSON.stringify(salesData)
                });

                if (data.success) {
                    processingOrders.delete(orderId);

                    // Remove the specific order card with animation
                    const orderCard = document.getElementById(`processing-order-${orderId}`);
                    if (orderCard) {
                        orderCard.style.transition = 'all 0.5s ease';
                        orderCard.style.transform = 'translateX(100%)';
                        orderCard.style.opacity = '0';

                        setTimeout(() => {
                            orderCard.remove();
                            updateProcessingHeader();

                            // If no more processing orders, reset panel
                            const remainingCards = document.querySelectorAll('.processing-order-card');
                            if (remainingCards.length === 0) {
                                resetProcessingPanel();
                            }
                        }, 500);
                    }

                    // Show temporary completion message
                    showOrderCompletionToast(processingOrder.orderNumber);
                } else {
                    throw new Error(data.message || 'Failed to complete order');
                }
            } catch (error) {
                console.error('Error completing order:', error);
                showErrorMessage('Failed to complete order: ' + error.message);
            }
        }

        function showOrderCompletionToast(orderNumber) {
            const displayOrderNumber = orderNumber || 'Unknown';

            const toast = document.createElement('div');
            toast.style.cssText = `
               position: fixed;
               top: 20px;
               right: 20px;
               background: #d4edda;
               color: #155724;
               border: 2px solid #c3e6cb;
               border-radius: 8px;
               padding: 15px 20px;
               box-shadow: 0 4px 15px rgba(21, 87, 36, 0.2);
               z-index: 3000;
               max-width: 350px;
               animation: slideIn 0.3s ease-out;
           `;

            toast.innerHTML = `
               <div style="display: flex; align-items: center; gap: 10px;">
                   <span style="font-size: 1.5em;">✅</span>
                   <div>
                       <strong>Order #${displayOrderNumber} Complete!</strong><br>
                       <small>Saved to sales database</small>
                   </div>
               </div>
           `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.remove();
                }
            }, 4000);
        }

        function resetProcessingPanel() {
            const processingSection = document.getElementById('processingSection');
            if (!processingSection) return;

            processingSection.className = 'processing-section';
            processingSection.innerHTML = `
               <div class="processing-icon">💳</div>
               <p><strong>SELECT AN ORDER</strong></p>
               <p>FROM THE LEFT PANEL TO</p>
               <p>BEGIN PROCESSING PAYMENT</p>
               <br>
               <p>Use the <strong>Accept</strong> button to process payment</p>
               <p>Use the <strong>Edit</strong> button to modify orders</p>
               <p>Use the <strong>Cancel</strong> button to cancel orders</p>
               <div style="margin-top: 20px; font-size: 0.8rem; color: #666; display: flex; align-items: center; justify-content: center; gap: 5px;">
                   <span>🔄</span>
                   <span>Auto-updating every 25 seconds</span>
               </div>
           `;
        }

        function cancelOrder(orderId) {
            currentAction = 'cancel';
            currentOrderId = orderId;

            showModal(
                'cancel',
                '❌',
                'Cancel Order',
                'This action cannot be undone',
                `Cancel Order #${orderId}?<br><br><strong>Warning:</strong> This will permanently cancel the order and cannot be undone.`,
                'Cancel Order'
            );
        }

        function showModal(type, icon, title, subtitle, message, confirmText) {
            const modal = document.getElementById('confirmModal');
            const header = document.getElementById('modalHeader');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('confirmBtn');

            if (!modal) {
                console.error('Confirm modal not found');
                return;
            }

            if (header) header.className = 'modal-header';
            if (confirmBtn) confirmBtn.className = 'modal-btn modal-btn-confirm';

            if (modalIcon) modalIcon.textContent = icon;
            if (modalTitle) modalTitle.textContent = title;
            if (modalSubtitle) modalSubtitle.textContent = subtitle;
            if (modalMessage) modalMessage.innerHTML = message;
            if (confirmBtn) confirmBtn.textContent = confirmText;

            if (type === 'cancel') {
                if (header) header.classList.add('cancel');
                if (confirmBtn) confirmBtn.classList.add('cancel-style');
            }

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function hideModal() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.remove('show');
            }
            document.body.style.overflow = '';

            currentAction = null;
            currentOrderId = null;
        }

        function confirmAction() {
            if (!currentAction || !currentOrderId) return;

            switch (currentAction) {
                case 'cancel':
                    processCancelOrder();
                    break;
            }

            hideModal();
        }

        async function processCancelOrder() {
            const orderCard = document.getElementById(`order-${currentOrderId}`);
            if (!orderCard) {
                showErrorMessage('Order card not found');
                return;
            }

            try {
                const data = await fetchWithErrorHandling('/cashier/cancel-order', {
                    method: 'POST',
                    body: JSON.stringify({
                        order_id: parseInt(currentOrderId),
                        reason: 'Cancelled by cashier'
                    })
                });

                if (data.success) {
                    orderCard.style.transition = 'all 0.5s ease';
                    orderCard.style.transform = 'translateX(-100%)';
                    orderCard.style.opacity = '0';

                    setTimeout(() => {
                        orderCard.remove();
                        checkEmptyState();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                showErrorMessage('Failed to cancel order: ' + error.message);
            }
        }

        function checkEmptyState() {
            const orderCards = document.querySelectorAll('.order-card');
            const emptyState = document.getElementById('emptyState');
            const ordersContainer = document.getElementById('ordersContainer');

            debugLog('Checking empty state', {
                orderCardsCount: orderCards.length,
                hasEmptyState: !!emptyState,
                hasContainer: !!ordersContainer
            });

            if (orderCards.length === 0 && ordersContainer) {
                if (!ordersContainer.contains(emptyState)) {
                    ordersContainer.innerHTML = `
                       <div class="empty-state" id="emptyState">
                           <div class="empty-state-icon">📭</div>
                           <p>No pending cash orders</p>
                           <small>Orders will appear here when customers place cash orders</small>
                           <div style="margin-top: 15px; font-size: 0.8rem; color: #666; display: flex; align-items: center; justify-content: center; gap: 5px;">
                               <span>🔄</span>
                               <span>Auto-updating every 25 seconds</span>
                           </div>
                       </div>
                   `;
                    debugLog('Empty state added to container');
                }
            } else if (orderCards.length > 0 && emptyState) {
                emptyState.remove();
                debugLog('Empty state removed - orders present');
            }
        }

        function showErrorMessage(message) {
            let errorAlert = document.getElementById('errorAlert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.id = 'errorAlert';
                errorAlert.style.cssText = `
                   position: fixed;
                   top: 20px;
                   right: 20px;
                   background: #f8d7da;
                   color: #721c24;
                   border: 2px solid #f5c6cb;
                   border-radius: 8px;
                   padding: 15px 20px;
                   box-shadow: 0 4px 15px rgba(114, 28, 36, 0.2);
                   z-index: 2000;
                   max-width: 400px;
                   animation: slideIn 0.3s ease-out;
               `;
                document.body.appendChild(errorAlert);
            }

            errorAlert.innerHTML = `
               <div style="display: flex; align-items: center; gap: 10px;">
                   <span style="font-size: 1.2em;">⚠️</span>
                   <div>
                       <strong>Error</strong><br>
                       ${message}
                   </div>
                   <button onclick="this.parentElement.parentElement.remove()" style="
                       background: none; 
                       border: none; 
                       color: #721c24; 
                       font-size: 1.2em; 
                       cursor: pointer;
                       margin-left: auto;
                   ">×</button>
               </div>
           `;

            setTimeout(() => {
                if (errorAlert && errorAlert.parentElement) {
                    errorAlert.remove();
                }
            }, 10000);
        }

        function showSuccessMessage(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
               position: fixed;
               top: 20px;
               right: 20px;
               background: #d4edda;
               color: #155724;
               border: 2px solid #c3e6cb;
               border-radius: 8px;
               padding: 15px 20px;
               box-shadow: 0 4px 15px rgba(21, 87, 36, 0.2);
               z-index: 3000;
               max-width: 350px;
               animation: slideIn 0.3s ease-out;
           `;

            toast.innerHTML = `
               <div style="display: flex; align-items: center; gap: 10px;">
                   <span style="font-size: 1.5em;">✅</span>
                   <div>
                       <strong>Success!</strong><br>
                       <small>${message}</small>
                   </div>
               </div>
           `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }

        // Printer testing functions
        async function testPrinterConnection() {
            try {
                showPrinterStatus('Testing printer connection...', 'processing');

                const response = await fetch('/test-printer-print');
                const data = await response.json();

                if (data.success) {
                    showPrinterStatus('✅ Printer test successful! Check if receipt printed.', 'success');
                    alert('✅ Printer test successful! Check if receipt printed.\n\nUsing printer: ' + (data.connection_info?.configured_printer_path || 'Unknown'));
                } else {
                    showPrinterStatus('❌ Printer test failed - check logs', 'error');
                    alert('❌ Printer test failed:\n' + data.error + '\n\nCheck browser console and Laravel logs for details.');
                }

                console.log('Printer test result:', data);
            } catch (error) {
                showPrinterStatus('❌ Printer test error: ' + error.message, 'error');
                alert('❌ Printer test error: ' + error.message);
                console.error('Printer test error:', error);
            }
        }

        async function showPrinterInfo() {
            try {
                const response = await fetch('/test-printer-connection');
                const data = await response.json();

                let message = 'Printer Configuration:\n\n';
                message += `Configured Printer: ${data.connection_info?.configured_printer_path || 'Not set'}\n`;
                message += `OS: ${data.connection_info?.os_family || 'Unknown'}\n\n`;

                if (data.connection_info?.system_printers?.length > 0) {
                    message += 'Available Printers:\n';
                    data.connection_info.system_printers.forEach((printer, index) => {
                        message += `${index + 1}. ${printer}\n`;
                    });
                } else {
                    message += 'No system printers detected\n';
                }

                alert(message);
                console.log('Printer info:', data);
            } catch (error) {
                alert('❌ Error getting printer info: ' + error.message);
                console.error('Printer info error:', error);
            }
        }

        // Event listeners
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                if (e.target.id === 'paymentModal') {
                    hidePaymentModal();
                } else if (e.target.id === 'editOrderModal') {
                    closeEditOrderModal();
                } else {
                    hideModal();
                }
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const paymentModal = document.getElementById('paymentModal');
                const confirmModal = document.getElementById('confirmModal');
                const editModal = document.getElementById('editOrderModal');

                if (paymentModal && paymentModal.classList.contains('show')) {
                    hidePaymentModal();
                } else if (editModal && editModal.classList.contains('show')) {
                    closeEditOrderModal();
                } else if (confirmModal && confirmModal.classList.contains('show')) {
                    hideModal();
                }
            }

            if (e.key === 'Enter') {
                const paymentModal = document.getElementById('paymentModal');
                if (paymentModal && paymentModal.classList.contains('show')) {
                    const confirmBtn = document.getElementById('confirmPaymentBtn');
                    if (confirmBtn && !confirmBtn.disabled && !isProcessingPayment) {
                        confirmPayment();
                    }
                }
            }
        });



        document.addEventListener('DOMContentLoaded', function () {
            debugLog('Cashier page loaded, initializing...');
            checkEmptyState();

            // Start auto-reload
            startAutoReload();

            // Do initial refresh after a short delay
            setTimeout(autoRefreshOrders, 10000);


        });

        // Stop auto-reload when page is about to unload
        window.addEventListener('beforeunload', function () {
            stopAutoReload();
        });

        function debugCSRF() {
            const token = getCSRFToken();
            console.log('CSRF Token:', token);
            console.log('Meta tag exists:', !!document.querySelector('meta[name="csrf-token"]'));
            console.log('Token length:', token ? token.length : 0);

            // Test if token is valid
            if (token && token.length !== 40) {
                console.warn('CSRF token length seems incorrect. Expected 40 characters, got:', token.length);
            }
        }

        // Call this function when page loads
        document.addEventListener('DOMContentLoaded', debugCSRF);

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
           @keyframes slideIn {
               from {
                   transform: translateY(-20px);
                   opacity: 0;
               }
               to {
                   transform: translateY(0);
                   opacity: 1;
               }
           }
           
           @keyframes slideInFromLeft {
               from {
                   transform: translateX(-100%);
                   opacity: 0;
               }
               to {
                   transform: translateX(0);
                   opacity: 1;
               }
           }

           @keyframes slideInNotification {
               from {
                   transform: translateX(100%);
                   opacity: 0;
               }
               to {
                   transform: translateX(0);
                   opacity: 1;
               }
           }
       `;
        document.head.appendChild(style);

        // Global function exports
        window.logout = logout;
        window.hideLogoutModal = hideLogoutModal;
        window.confirmLogout = confirmLogout;
        window.acceptOrder = acceptOrder;
        window.editOrder = editOrder;
        window.cancelOrder = cancelOrder;
        window.hidePaymentModal = hidePaymentModal;
        window.hideModal = hideModal;
        window.confirmPayment = confirmPayment;
        window.confirmAction = confirmAction;
        window.calculateChange = calculateChange;
        window.setQuickCash = setQuickCash;
        window.resetProcessingPanel = resetProcessingPanel;
        window.completeOrder = completeOrder;
        window.moveBackToPending = moveBackToPending;
        window.closeEditOrderModal = closeEditOrderModal;
        window.addEditItemToOrder = addEditItemToOrder;
        window.removeEditItem = removeEditItem;
        window.increaseEditQuantity = increaseEditQuantity;
        window.decreaseEditQuantity = decreaseEditQuantity;
        window.saveEditOrderChanges = saveEditOrderChanges;
        window.filterEditMenuItems = filterEditMenuItems;
        window.testPrinterConnection = testPrinterConnection;
        window.showPrinterInfo = showPrinterInfo;

        // Initialize Pusher for real-time Maya payment notifications
        document.addEventListener('DOMContentLoaded', function () {
            const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
            const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content;

            if (!pusherKey) {
                console.warn('Pusher not configured. Real-time notifications disabled.');
                return;
            }

            // Initialize Pusher
            const pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true
            });

            // Subscribe to orders channel
            const channel = pusher.subscribe('orders');

            // Listen for payment-received event
            channel.bind('payment-received', function (data) {
                console.log('Maya payment received:', data);

                const order = data.order;

                // Show notification
                showMayaPaymentNotification(order);

                // Play sound
                playNotificationSound();

                // Refresh orders list after 2 seconds
                setTimeout(() => {
                    if (typeof autoRefreshOrders === 'function') {
                        autoRefreshOrders();
                    }
                }, 2000);
            });

            console.log('Real-time payment notifications enabled');
        });

        function showMayaPaymentNotification(order) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        min-width: 320px;
        animation: slideInFromRight 0.3s ease-out;
    `;

            notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 32px;">💳</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 16px; margin-bottom: 4px;">
                    Maya Payment Received!
                </div>
                <div style="opacity: 0.95; font-size: 14px;">
                    Order #${order.order_number}
                </div>
                <div style="font-weight: 700; font-size: 20px; margin-top: 4px;">
                    ₱${parseFloat(order.total_amount).toFixed(2)}
                </div>
                <div style="font-size: 12px; margin-top: 4px; opacity: 0.9;">
                    ✅ Payment Verified
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: rgba(255,255,255,0.2); border: none; color: white; 
                           width: 28px; height: 28px; border-radius: 50%; cursor: pointer; 
                           font-size: 18px; display: flex; align-items: center; 
                           justify-content: center;">
                ×
            </button>
        </div>
    `;

            // Add animation
            const style = document.createElement('style');
            style.textContent = `
        @keyframes slideInFromRight {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
            if (!document.getElementById('maya-animation-styles')) {
                style.id = 'maya-animation-styles';
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.style.transition = 'all 0.3s ease-out';
                notification.style.transform = 'translateX(400px)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        function playNotificationSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();

                // First beep
                const oscillator1 = audioContext.createOscillator();
                const gainNode1 = audioContext.createGain();
                oscillator1.connect(gainNode1);
                gainNode1.connect(audioContext.destination);
                oscillator1.frequency.value = 800;
                oscillator1.type = 'sine';
                gainNode1.gain.value = 0.3;
                oscillator1.start();
                oscillator1.stop(audioContext.currentTime + 0.1);

                // Second beep
                setTimeout(() => {
                    const oscillator2 = audioContext.createOscillator();
                    const gainNode2 = audioContext.createGain();
                    oscillator2.connect(gainNode2);
                    gainNode2.connect(audioContext.destination);
                    oscillator2.frequency.value = 1000;
                    oscillator2.type = 'sine';
                    gainNode2.gain.value = 0.3;
                    oscillator2.start();
                    oscillator2.stop(audioContext.currentTime + 0.1);
                }, 150);
            } catch (error) {
                console.warn('Could not play notification sound:', error);
            }
        }

    </script>
    <!-- Logout Confirmation Modal -->
    <div class="logout-modal-overlay" id="logoutModal">
        <div class="logout-modal">
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to logout?</p>
            <div class="logout-modal-actions">
                <button class="logout-modal-btn logout-modal-btn-cancel" onclick="hideLogoutModal()">
                    Cancel
                </button>
                <button class="logout-modal-btn logout-modal-btn-confirm" onclick="confirmLogout()">
                    Logout
                </button>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('js/maya-payment-monitor.js')); ?>"></script>
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/cashier.blade.php ENDPATH**/ ?>