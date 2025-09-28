<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>L'PRIMERO CAFE - Menu</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        /* All your existing styles remain the same until cart section... */
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

        /* Add this to your existing CSS */
        .btn,
        .action-btn,
        button,
        .dine-in-btn,
        .take-out-btn {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: pointer !important;
            touch-action: manipulation;
        }

        /* Fix for tablet touch events */
        @media (pointer: coarse) {

            .btn,
            .action-btn,
            button {
                min-height: 48px;
                min-width: 48px;
                padding: 15px 20px !important;
            }
        }

        .kiosk-container {
            height: 100vh;
            display: flex;
            background: #f5f1e8;
            width: 100%;
            overflow: hidden;
        }

        /* Enhanced Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(3px);
        }

        /* Enhanced Modal Container */
        .modal-container {
            background: #ffffff;
            width: 450px;
            max-width: 95vw;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Enhanced Modal Content */
        .modal-content {
            padding: 40px 30px 30px;
            text-align: center;
        }

        .item-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: #2c1810;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .item-price {
            font-size: 1.3rem;
            font-weight: 600;
            color: #8b4513;
            margin: 0 0 30px 0;
        }

        /* Enhanced Item Image */
        .item-image {
            position: relative;
            margin: 0 0 30px 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .item-image img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .item-image:hover img {
            transform: scale(1.05);
        }

        /* Enhanced Quantity Section */
        .quantity-section {
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }

        .quantity-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 15px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 25px;
        }

        .quantity-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #8b4513;
            background: white;
            border-radius: 50%;
            font-size: 20px;
            font-weight: bold;
            color: #8b4513;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(139, 69, 19, 0.1);
        }

        .quantity-btn:hover {
            background: #8b4513;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.2);
        }

        .quantity-btn:active {
            transform: translateY(0);
        }

        .quantity-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c1810;
            min-width: 40px;
            background: white;
            padding: 8px 16px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        /* Enhanced Modal Bottom */
        .modal-bottom {
            background: #f8f9fa;
            padding: 25px 30px;
        }

        .addon-btn {
            width: 100%;
            padding: 15px;
            background: #ffffff;
            border: 2px solid #8b4513;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #8b4513;
            cursor: pointer;
            margin-bottom: 25px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .addon-btn:hover {
            background: #8b4513;
            color: white;
            transform: translateY(-1px);
        }

        /* Enhanced Order Summary */
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px 20px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .total-info,
        .quantity-info {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c1810;
            letter-spacing: 0.5px;
        }

        .total-price {
            color: #8b4513;
            font-size: 1.1rem;
            font-weight: 700;
        }

        /* Enhanced Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .cancel-btn,
        .add-to-cart-btn {
            flex: 1;
            padding: 18px 15px;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        .cancel-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .add-to-cart-btn {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        .add-to-cart-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .add-to-cart-btn:hover::before {
            left: 100%;
        }

        /* ADD-ON MODAL STYLES */
        .addon-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1100;
            backdrop-filter: blur(4px);
        }

        .addon-modal-container {
            background: #ffffff;
            width: 500px;
            max-width: 95vw;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: modalSlideIn 0.3s ease-out;
            max-height: 80vh;
            overflow-y: auto;
        }

        .addon-modal-header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .addon-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: 1px;
        }

        .addon-modal-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
            font-weight: 400;
        }

        .addon-modal-content {
            padding: 30px;
        }

        .addon-category {
            margin-bottom: 30px;
        }

        .addon-category:last-child {
            margin-bottom: 0;
        }

        .addon-category-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 8px;
        }

        .addon-options {
            display: grid;
            gap: 12px;
        }

        .addon-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .addon-option:hover {
            background: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .addon-option.selected {
            background: #fff8f0;
            border-color: #8b4513;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        }

        .addon-option-info {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .addon-option-name {
            font-weight: 600;
            color: #2c1810;
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .addon-option-description {
            font-size: 0.85rem;
            color: #666;
            opacity: 0.8;
        }

        .addon-option-price {
            font-weight: 700;
            color: #8b4513;
            font-size: 1rem;
            margin-left: 15px;
        }

        .addon-option-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #8b4513;
            border-radius: 4px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .addon-option.selected .addon-option-checkbox {
            background: #8b4513;
            color: white;
        }

        .addon-option.selected .addon-option-checkbox::after {
            content: '✓';
            font-size: 12px;
            font-weight: bold;
        }

        .addon-modal-footer {
            background: #f8f9fa;
            padding: 25px 30px;
            border-top: 1px solid #e9ecef;
        }

        .addon-total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .addon-total-label {
            font-size: 1rem;
            font-weight: 600;
            color: #2c1810;
        }

        .addon-total-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #8b4513;
        }

        .addon-modal-actions {
            display: flex;
            gap: 15px;
        }

        .addon-cancel-btn,
        .addon-confirm-btn {
            flex: 1;
            padding: 18px 15px;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .addon-cancel-btn {
            background: #6c757d;
            color: white;
        }

        .addon-cancel-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .addon-confirm-btn {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
        }

        .addon-confirm-btn:hover {
            background: linear-gradient(135deg, #7a3d10, #8b4513);
            transform: translateY(-2px);
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
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #d4c4a8;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c1810;
        }

        .category-list {
            flex: 1;
            padding: 20px 0;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 20px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 5px 15px;
        }

        .category-item:hover {
            background: rgba(44, 24, 16, 0.1);
        }

        .category-item.active {
            background: rgba(44, 24, 16, 0.15);
            border-left: 4px solid #8b4513;
        }

        .category-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 8px;
            object-fit: cover;
            border: 2px solid #d4c4a8;
        }

        .category-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2c1810;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            min-width: 0;
            flex: 1;
            overflow-x: auto;
        }

        .menu-header {
            padding: 30px;
            text-align: center;
            background: #F5E6D3;
            border-bottom: 1px solid #d4c4a8;
            position: relative;
        }

        .menu-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 3px;
        }

        /* Order Type Dropdown */
        .order-type-dropdown {
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .dropdown-btn {
            background: #8b4513;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .dropdown-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .dropdown-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .dropdown-btn.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #8b4513;
            border-radius: 8px;
            margin-top: 5px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-weight: 500;
            color: #2c1810;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #F5E6D3;
        }

        .dropdown-item:first-child {
            border-radius: 6px 6px 0 0;
        }

        .dropdown-item:last-child {
            border-radius: 0 0 6px 6px;
        }

        .dropdown-item.selected {
            background: #8b4513;
            color: white;
        }

        .dropdown-item.selected:hover {
            background: #6d3410;
        }

        /* Products Grid */
        .products-section {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            max-width: 1200px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            display: block;
        }

        .product-card.hide {
            display: none !important;
        }

        .product-card.show {
            display: block !important;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #8b4513;
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .product-info {
            padding: 15px;
            text-align: center;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #8b4513;
        }

        /* ENHANCED CART SECTION */
        .cart-section {
            background: white;
            border-top: 2px solid #d4c4a8;
            padding: 20px 30px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            bottom: 0;
            left: 200px;
            right: 0;
            z-index: 1000;
            max-height: 60vh;
            overflow-y: auto;
        }

        .cart-buttons {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-top: 0;
            margin-top: -20px;
        }

        .close-cart-btn {
            background: #8b4513;
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 0 auto;
            margin-top: 0;
            min-height: 50px;
        }

        /* Cart Items List */
        .cart-items {
            margin-bottom: 20px;
            max-height: 200px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            border-color: #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            background: #e9ecef;
        }

        .cart-item-details {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2c1810;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .cart-item-addons {
            font-size: 0.75rem;
            color: #666;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .cart-item-quantity-price {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid #e9ecef;
        }

        .cart-quantity-btn {
            width: 24px;
            height: 24px;
            border: none;
            background: #8b4513;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .cart-quantity-btn:hover {
            background: #6d3410;
        }

        .cart-quantity-display {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c1810;
            min-width: 20px;
            text-align: center;
        }

        .cart-item-price {
            font-size: 1rem;
            font-weight: 700;
            color: #8b4513;
        }

        .cart-item-remove {
            width: 32px;
            height: 32px;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            transition: all 0.3s ease;
        }

        .cart-item-remove:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .cart-section.minimized {
            height: 50px;
            padding: 5px 30px;
            overflow: hidden;
        }

        .cart-section.minimized .cart-buttons {
            margin-bottom: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .cart-section.minimized .cart-items,
        .cart-section.minimized .cart-total,
        .cart-section.minimized .checkout-actions {
            display: none;
        }

        .close-cart-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .category-prompt {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            color: #8b4513;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .category-prompt h2 {
            margin: 0;
            padding: 20px;
            background: rgba(245, 230, 211, 0.8);
            border-radius: 10px;
            border: 2px solid #d4c4a8;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }

        .total-label {
            color: #2c1810;
        }

        .total-amount {
            color: #8b4513;
        }

        .checkout-actions {
            display: flex;
            gap: 15px;
        }

        .checkout-btn,
        .cancel-btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .checkout-btn {
            background: #8b4513;
            color: white;
        }

        .checkout-btn:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #e0e0e0;
            color: #666;
        }

        .cancel-btn:hover {
            background: #d0d0d0;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .modal-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-btn-yes {
            background: #2c1810;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #1a0f08;
            transform: translateY(-2px);
        }

        .modal-btn-no {
            background: white;
            color: #2c1810;
            border: 2px solid #d4c4a8;
        }

        .modal-btn-no:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .order-type-dropdown {
                top: 15px;
                right: 15px;
            }

            .dropdown-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
                min-width: 100px;
            }

            .menu-title {
                font-size: 2rem;
                margin-right: 140px;
            }

            .sidebar {
                width: 150px !important;
                flex-shrink: 0;
                flex-grow: 0;
            }

            .kiosk-container {
                flex-wrap: nowrap;
            }

            .main-content {
                min-width: 0;
                flex: 1;
            }

            .products-section {
                flex: 1;
                padding: 30px;
                padding-bottom: 120px;
                overflow-y: auto;
            }

            .cart-item-image {
                width: 50px;
                height: 50px;
            }

            .cart-item-name {
                font-size: 0.9rem;
            }

            .cart-item-addons {
                font-size: 0.7rem;
            }
        }

        /* Lenovo Xiaoxin Pad 2024 11" Optimizations */
        @media (min-width: 1200px) and (max-width: 1920px) {
            .menu-title {
                font-size: 3rem;
            }

            .category-item {
                padding: 20px;
                margin: 8px 15px;
            }

            .category-image {
                width: 70px;
                height: 70px;
            }

            .category-name {
                font-size: 1rem;
            }

            .product-card {
                min-height: 280px;
            }

            .product-image {
                height: 180px;
            }

            .product-info {
                padding: 20px;
            }

            .product-name {
                font-size: 1.2rem;
            }

            .product-price {
                font-size: 1.3rem;
            }

            .dropdown-btn {
                padding: 15px 25px;
                font-size: 1.2rem;
                min-width: 150px;
            }

            .checkout-btn,
            .cancel-btn {
                padding: 20px;
                font-size: 1.3rem;
            }

            .close-cart-btn {
                padding: 22px 40px;
                font-size: 1.2rem;
                min-height: 60px;
            }

            .cart-item-image {
                width: 70px;
                height: 70px;
            }

            .cart-item-name {
                font-size: 1.1rem;
            }

            .cart-item-price {
                font-size: 1.1rem;
            }
        }

        /* Portrait mode optimization for tablet */
        @media (orientation: portrait) and (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }

            .sidebar {
                width: 180px !important;
                min-width: 180px;
                max-width: 180px;
            }

            .cart-section {
                left: 180px;
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

            <div class="category-list">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="category-item" data-category="<?php echo e(strtolower(str_replace(' ', '_', $category->name))); ?>"
                        data-category-id="<?php echo e($category->id); ?>">
                        <?php if($category->image && file_exists(public_path('assets/' . $category->image))): ?>
                            <img src="<?php echo e(asset('assets/' . $category->image)); ?>" alt="<?php echo e($category->name); ?>"
                                class="category-image">
                        <?php else: ?>
                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%238B4513' rx='8'/><circle cx='30' cy='30' r='15' fill='%23D2691E'/><circle cx='30' cy='25' r='8' fill='%23F4A460'/></svg>"
                                alt="<?php echo e($category->name); ?>" class="category-image">
                        <?php endif; ?>
                        <span class="category-name"><?php echo e(str_replace('_', ' ', $category->name)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Menu Header -->
            <header class="menu-header">
                <h1 class="menu-title">MENU</h1>

                <!-- Order Type Dropdown -->
                <div class="order-type-dropdown">
                    <button class="dropdown-btn" id="orderTypeBtn">
                        <span id="selectedOrderType"><?php echo e(strtoupper(str_replace('-', ' ', $orderType))); ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="dropdown-menu" id="orderTypeMenu">
                        <div class="dropdown-item <?php echo e($orderType === 'dine-in' ? 'selected' : ''); ?>" data-type="dine-in">
                            DINE IN</div>
                        <div class="dropdown-item <?php echo e($orderType === 'take-out' ? 'selected' : ''); ?>"
                            data-type="take-out">TAKE OUT</div>
                    </div>
                </div>
            </header>

            <!-- Products Section -->
            <section class="products-section">
                <div class="products-grid" id="productsGrid">
                    <div id="categoryPrompt" class="category-prompt">
                        <h2>PLEASE CHOOSE FROM CATEGORY</h2>
                    </div>
                    <?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="menu-item cursor-pointer" data-name="<?php echo e($item->name); ?>"
                            data-price="<?php echo e(number_format($item->price, 2)); ?>"
                            data-image="<?php echo e($item->image && file_exists(public_path('assets/' . $item->image)) ? asset('assets/' . $item->image) : ''); ?>"
                            data-description="<?php echo e($item->description ?? ''); ?>">
                            <div class="product-card hide"
                                data-category="<?php echo e(strtolower(str_replace(' ', '_', $item->category->name ?? ''))); ?>"
                                data-id="<?php echo e($item->id); ?>" data-has-variants="<?php echo e($item->has_variants ? 'true' : 'false'); ?>">
                                <?php if($item->image && file_exists(public_path('assets/' . $item->image))): ?>
                                    <img src="<?php echo e(asset('assets/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>"
                                        class="product-image">
                                <?php else: ?>
                                    <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>"
                                        alt="<?php echo e($item->name); ?>" class="product-image">
                                <?php endif; ?>

                                <div class="product-info">
                                    <h3 class="product-name"><?php echo e($item->name); ?></h3>
                                    <p class="product-price">PHP <?php echo e(number_format($item->price, 2)); ?></p>
                                    <?php if($item->description): ?>
                                        <p class="product-description" style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                                            <?php echo e(Str::limit($item->description, 50)); ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <!-- Enhanced Cart Section -->
            <footer class="cart-section">
                <div class="cart-buttons">
                    <button class="close-cart-btn" id="closeCartBtn">
                        V HIDE CART
                    </button>
                </div>

                <!-- Cart Items List -->
                <div class="cart-items" id="cartItems">
                    <!-- Cart items will be populated here -->
                </div>

                <div class="cart-total">
                    <span class="total-label">TOTAL:</span>
                    <span class="total-amount" id="totalAmount">PHP 0.00</span>
                </div>

                <div class="checkout-actions">
                    <button class="checkout-btn" id="checkoutBtn">CHECKOUT</button>
                    <button class="cancel-btn" id="cancelBtn">CANCEL</button>
                </div>
            </footer>
        </main>
    </div>

    <!-- All your existing modals remain the same... -->
    <!-- Custom Modal -->
    <div class="modal-overlay" id="cancelModal" style="display: none;">
        <div class="modal-content">
            <h3 class="modal-title">CANCEL ORDERING?</h3>
            <button class="modal-btn modal-btn-yes" id="confirmYes">YES, CANCEL MY ORDER</button>
            <button class="modal-btn modal-btn-no" id="confirmNo">NO</button>
        </div>
    </div>

    <!-- Enhanced Item Details Modal -->
    <div id="itemDetailsModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-content">
                <h2 class="item-title">Americano</h2>
                <p class="item-price">PHP 95.00</p>

                <div class="item-image">
                    <img src="" alt="Item Image">
                </div>

                <div class="quantity-section">
                    <div class="quantity-label">Quantity</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity(-1)">−</button>
                        <span class="quantity-display" id="quantity">1</span>
                        <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
                    </div>
                </div>
            </div>

            <div class="modal-bottom">
                <button class="addon-btn" onclick="openAddonModal()">Add-On</button>

                <div class="order-summary">
                    <div class="total-info">Total: <span class="total-price" id="totalPrice">PHP 95.00</span></div>
                    <div class="quantity-info">Quantity: <span id="summaryQuantity">1</span></div>
                </div>

                <div class="action-buttons">
                    <button class="cancel-btn" onclick="closeItemModal()">Cancel</button>
                    <button class="add-to-cart-btn" id="addToCartButton"
                        onclick="addToCart(currentItem, null, {quantity: currentQuantity, addons: selectedAddons})">Add
                        to Cart</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add-on Selection Modal -->
    <div id="addonModal" class="addon-modal-overlay" style="display: none;">
        <div class="addon-modal-container">
            <div class="addon-modal-header">
                <h2 class="addon-modal-title">Customize Your Order</h2>
                <p class="addon-modal-subtitle">Select your preferred add-ons</p>
            </div>

            <div class="addon-modal-content" id="addonModalContent">
                <!-- Add-on categories will be populated here -->
            </div>

            <div class="addon-modal-footer">
                <div class="addon-total-section">
                    <span class="addon-total-label">Add-ons Total:</span>
                    <span class="addon-total-price" id="addonTotalPrice">PHP 0.00</span>
                </div>

                <div class="addon-modal-actions">
                    <button class="addon-cancel-btn" onclick="closeAddonModal()">Cancel</button>
                    <button class="addon-confirm-btn" onclick="confirmAddons()">Confirm Add-ons</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let total = 0;
        let currentOrderType = '<?php echo e($orderType ?? "dine-in"); ?>';
        let menuItems = <?php echo json_encode($menuItems, 15, 512) ?>;
        let categories = <?php echo json_encode($categories, 15, 512) ?>;
        let currentQuantity = 1;
        let currentSize = 'medium';
        let basePrice = 0;
        let currentItem = null;
        let selectedAddons = [];
        let addonTotal = 0;

        // Define add-ons for different menu items
        const menuAddons = {
            // Coffee items
            'coffee': {
                'Milk Options': [
                    { name: 'Oat Milk', description: 'Creamy plant-based alternative', price: 15 },
                    { name: 'Almond Milk', description: 'Light and nutty flavor', price: 15 },
                    { name: 'Coconut Milk', description: 'Rich tropical taste', price: 20 },
                    { name: 'Soy Milk', description: 'Classic dairy alternative', price: 10 }
                ],
                'Sweeteners': [
                    { name: 'Extra Sugar', description: 'Additional sweetness', price: 0 },
                    { name: 'Honey', description: 'Natural golden sweetener', price: 10 },
                    { name: 'Stevia', description: 'Zero-calorie natural sweetener', price: 5 },
                    { name: 'Brown Sugar', description: 'Rich molasses flavor', price: 5 }
                ],
                'Extras': [
                    { name: 'Extra Shot', description: 'Double the caffeine', price: 25 },
                    { name: 'Decaf', description: 'Caffeine-free option', price: 0 },
                    { name: 'Extra Hot', description: 'Served extra hot', price: 0 },
                    { name: 'Extra Foam', description: 'Extra creamy foam', price: 5 }
                ]
            },
            // Sweet treats
            'sweet_treats': {
                'Toppings': [
                    { name: 'Whipped Cream', description: 'Light and fluffy topping', price: 15 },
                    { name: 'Chocolate Chips', description: 'Mini chocolate morsels', price: 20 },
                    { name: 'Caramel Drizzle', description: 'Sweet caramel sauce', price: 15 },
                    { name: 'Nuts', description: 'Mixed crushed nuts', price: 25 }
                ],
                'Sauces': [
                    { name: 'Chocolate Sauce', description: 'Rich chocolate drizzle', price: 10 },
                    { name: 'Strawberry Sauce', description: 'Fresh berry flavor', price: 10 },
                    { name: 'Vanilla Sauce', description: 'Classic vanilla drizzle', price: 10 }
                ]
            },
            // Rice meals
            'rice_meals': {
                'Sides': [
                    { name: 'Extra Rice', description: 'Additional serving of rice', price: 20 },
                    { name: 'Garlic Rice', description: 'Fragrant garlic-infused rice', price: 30 },
                    { name: 'Soup', description: 'Hot soup of the day', price: 35 },
                    { name: 'Salad', description: 'Fresh mixed greens', price: 40 }
                ],
                'Proteins': [
                    { name: 'Extra Meat', description: 'Double the protein', price: 50 },
                    { name: 'Fried Egg', description: 'Sunny side up egg', price: 25 },
                    { name: 'Grilled Vegetables', description: 'Seasonal grilled veggies', price: 35 }
                ]
            },
            // Default for other categories
            'default': {
                'Extras': [
                    { name: 'Extra Sauce', description: 'Additional flavor sauce', price: 10 },
                    { name: 'Extra Serving', description: 'Larger portion size', price: 30 }
                ]
            }
        };

        function openItemModal(item) {
            // Check if item is available
            fetch(`/api/check-availability/${item.id}`)
                .then(response => response.json())
                .then(data => {
                    const button = document.getElementById('addToCartButton');
                    if (data.available) {
                        button.disabled = false;
                        button.textContent = 'Add to Cart';
                        button.className = 'add-to-cart-btn';
                    } else {
                        button.disabled = true;
                        button.textContent = 'Out of Stock';
                        button.className = 'add-to-cart-btn disabled';
                    }
                });
        }

        function openItemModal() {
            document.getElementById('itemDetailsModal').style.display = 'flex';
        }

        function closeItemModal() {
            const modal = document.getElementById('itemDetailsModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            selectedAddons = [];
            addonTotal = 0;
        }

        function openAddonModal() {
            if (!currentItem) return;

            const modal = document.getElementById('addonModal');
            const content = document.getElementById('addonModalContent');

            // Determine which add-ons to show based on item category
            let itemCategory = 'default';
            if (currentItem.category) {
                itemCategory = currentItem.category.toLowerCase().replace(' ', '_');
            }

            // Get add-ons for this category, fallback to default
            const addons = menuAddons[itemCategory] || menuAddons['default'];

            // Clear previous content
            content.innerHTML = '';

            // Populate add-on categories
            Object.keys(addons).forEach(categoryName => {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'addon-category';

                const categoryTitle = document.createElement('h3');
                categoryTitle.className = 'addon-category-title';
                categoryTitle.textContent = categoryName;
                categoryDiv.appendChild(categoryTitle);

                const optionsDiv = document.createElement('div');
                optionsDiv.className = 'addon-options';

                addons[categoryName].forEach((addon, index) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'addon-option';
                    optionDiv.dataset.category = categoryName;
                    optionDiv.dataset.index = index;

                    optionDiv.innerHTML = `
                        <div class="addon-option-checkbox"></div>
                        <div class="addon-option-info">
                            <div class="addon-option-name">${addon.name}</div>
                            <div class="addon-option-description">${addon.description}</div>
                        </div>
                        <div class="addon-option-price">+PHP ${addon.price.toFixed(2)}</div>
                    `;

                    optionDiv.addEventListener('click', function () {
                        toggleAddon(this, addon);
                    });

                    optionsDiv.appendChild(optionDiv);
                });

                categoryDiv.appendChild(optionsDiv);
                content.appendChild(categoryDiv);
            });

            modal.style.display = 'flex';
            updateAddonTotal();
        }

        function closeAddonModal() {
            document.getElementById('addonModal').style.display = 'none';
        }


        // Add this JavaScript to fix touch events
        document.addEventListener('DOMContentLoaded', function () {
            // Fix touch events for all buttons
            const buttons = document.querySelectorAll('button, .btn, .action-btn');

            buttons.forEach(button => {
                // Add touch event listeners
                button.addEventListener('touchstart', function (e) {
                    e.preventDefault();
                    this.style.transform = 'scale(0.98)';
                });

                button.addEventListener('touchend', function (e) {
                    e.preventDefault();
                    this.style.transform = 'scale(1)';

                    // Trigger click after a short delay
                    setTimeout(() => {
                        this.click();
                    }, 50);
                });

                // Add visual feedback
                button.addEventListener('touchstart', function () {
                    this.classList.add('active');
                });

                button.addEventListener('touchend', function () {
                    setTimeout(() => {
                        this.classList.remove('active');
                    }, 150);
                });
            });
        });

        function toggleAddon(element, addon) {
            element.classList.toggle('selected');

            const isSelected = element.classList.contains('selected');
            const addonKey = `${addon.name}_${addon.price}`;

            if (isSelected) {
                selectedAddons.push({
                    name: addon.name,
                    description: addon.description,
                    price: addon.price
                });
            } else {
                selectedAddons = selectedAddons.filter(item =>
                    `${item.name}_${item.price}` !== addonKey
                );
            }

            updateAddonTotal();
        }

        function updateAddonTotal() {
            addonTotal = selectedAddons.reduce((total, addon) => total + addon.price, 0);
            const addonTotalEl = document.getElementById('addonTotalPrice');
            if (addonTotalEl) {
                addonTotalEl.textContent = `PHP ${addonTotal.toFixed(2)}`;
            }
        }

        function confirmAddons() {
            closeAddonModal();
            // Update the total price in the main modal
            updateTotalPriceWithAddons();
        }

        function updateTotalPriceWithAddons() {
            const totalPriceEl = document.getElementById('totalPrice');
            if (totalPriceEl && basePrice) {
                const totalWithAddons = (basePrice * currentQuantity) + (addonTotal * currentQuantity);
                totalPriceEl.textContent = `PHP ${totalWithAddons.toFixed(2)}`;
            }
        }

        // ENHANCED CART FUNCTIONS
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            if (!cartItemsContainer) return;

            cartItemsContainer.innerHTML = '';

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Your cart is empty</p>';
                return;
            }

            cart.forEach((item, index) => {
                const cartItemDiv = document.createElement('div');
                cartItemDiv.className = 'cart-item';

                // Use the stored image URL from cart item with better handling
                let imageUrl = "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 60'><rect width='60' height='60' fill='%23F5F5F5'/><circle cx='30' cy='30' r='15' fill='%238B4513'/></svg>";

                if (item.image && item.image !== '') {
                    imageUrl = item.image;
                    // Ensure it has the proper path
                    if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/assets/') && !imageUrl.startsWith('data:')) {
                        imageUrl = `/assets/${imageUrl}`;
                    }
                }

                console.log(`Cart item ${index} - Image URL:`, imageUrl); // Debug log

                // Format add-ons text
                const addonsText = item.addons && item.addons.length > 0
                    ? item.addons.map(addon => addon.name).join(', ')
                    : '';

                cartItemDiv.innerHTML = `
            <img src="${imageUrl}" alt="${item.name}" class="cart-item-image" onerror="this.src='data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 60 60%27><rect width=%2760%27 height=%2760%27 fill=%27%23F5F5F5%27/><circle cx=%2730%27 cy=%2730%27 r=%2715%27 fill=%27%238B4513%27/></svg>'">
            <div class="cart-item-details">
                <div class="cart-item-name">${item.name}</div>
                ${addonsText ? `<div class="cart-item-addons">Add-ons: ${addonsText}</div>` : ''}
                <div class="cart-item-quantity-price">
                    <div class="cart-item-quantity">
                        <button class="cart-quantity-btn" onclick="updateCartItemQuantity(${index}, -1)">-</button>
                        <span class="cart-quantity-display">${item.quantity}</span>
                        <button class="cart-quantity-btn" onclick="updateCartItemQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-price">PHP ${((item.price + item.addonsPrice) * item.quantity).toFixed(2)}</div>
                </div>
            </div>
            <button class="cart-item-remove" onclick="removeCartItem(${index})">×</button>
        `;

                cartItemsContainer.appendChild(cartItemDiv);
            });
        }

        function updateCartItemQuantity(index, change) {
            if (cart[index]) {
                cart[index].quantity += change;

                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }

                updateTotal();
                updateCartDisplay();
            }
        }

        function removeCartItem(index) {
            if (cart[index]) {
                cart.splice(index, 1);
                updateTotal();
                updateCartDisplay();
            }
        }

        // Order Type Dropdown functionality
        const orderTypeBtn = document.getElementById('orderTypeBtn');
        const orderTypeMenu = document.getElementById('orderTypeMenu');
        const selectedOrderTypeSpan = document.getElementById('selectedOrderType');

        // Toggle dropdown
        orderTypeBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            orderTypeBtn.classList.toggle('active');
            orderTypeMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            orderTypeBtn?.classList.remove('active');
            orderTypeMenu?.classList.remove('active');
        });

        // Handle dropdown item selection
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function () {
                const selectedType = this.dataset.type;
                const displayText = this.textContent;

                // Update button text
                const dropdownBtnSpan = document.querySelector('.dropdown-btn span');
                if (dropdownBtnSpan) dropdownBtnSpan.textContent = displayText;
                if (selectedOrderTypeSpan) selectedOrderTypeSpan.textContent = displayText;

                // Update selected state
                document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');

                // Close dropdown
                orderTypeBtn?.classList.remove('active');
                orderTypeMenu?.classList.remove('active');

                // Update order type
                updateOrderType(selectedType);
            });
        });

        // DOMContentLoaded event handler
        document.addEventListener('DOMContentLoaded', function () {
            // Set initial dropdown text based on order type
            const initialType = currentOrderType;
            const initialItem = document.querySelector(`[data-type="${initialType}"]`);
            if (initialItem) {
                initialItem.classList.add('selected');
            }

            // Hide all products initially and show category prompt
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('show');
                card.classList.add('hide');
                card.style.display = 'none';
            });

            // Show the category prompt
            const categoryPrompt = document.getElementById('categoryPrompt');
            if (categoryPrompt) {
                categoryPrompt.style.display = 'block';
            }

            // Initialize cart display
            updateCartDisplay();
        });

        // Function to update quantity in modal
        function updateQuantity(change) {
            if (change === 1) {
                currentQuantity++;
            } else if (change === -1 && currentQuantity > 1) {
                currentQuantity--;
            }

            // Update displays
            const quantityEl = document.getElementById('quantity');
            const summaryQuantityEl = document.getElementById('summaryQuantity');

            if (quantityEl) quantityEl.textContent = currentQuantity;
            if (summaryQuantityEl) summaryQuantityEl.textContent = currentQuantity;

            updateTotalPriceWithAddons();
        }

        function openItemModalFromData(element) {
            const name = element.dataset.name;
            const price = element.dataset.price;
            const image = element.dataset.image;
            const description = element.dataset.description;

            // Find the actual menu item to get category info
            const productCard = element.querySelector('.product-card');
            const itemId = parseInt(productCard.dataset.id);
            const menuItemData = menuItems.find(item => item.id === itemId);

            currentItem = {
                id: itemId,
                name: name,
                price: parseFloat(price),
                image: image, // This should be the full URL from dataset
                description: description,
                category: menuItemData ? menuItemData.category?.name : null
            };

            basePrice = parseFloat(price);
            currentQuantity = 1;
            selectedAddons = [];
            addonTotal = 0;

            // Update modal elements
            const modal = document.getElementById('itemDetailsModal');
            if (modal) {
                const titleEl = modal.querySelector('.item-title');
                const priceEl = modal.querySelector('.item-price');
                const imageEl = modal.querySelector('.item-image img');
                const quantityEl = modal.querySelector('#quantity');
                const summaryQuantityEl = modal.querySelector('#summaryQuantity');
                const totalPriceEl = modal.querySelector('#totalPrice');

                if (titleEl) titleEl.textContent = name;
                if (priceEl) priceEl.textContent = `PHP ${parseFloat(price).toFixed(2)}`;
                if (imageEl) {
                    imageEl.src = image || "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>";
                }

                if (quantityEl) quantityEl.textContent = '1';
                if (summaryQuantityEl) summaryQuantityEl.textContent = '1';
                if (totalPriceEl) totalPriceEl.textContent = `PHP ${parseFloat(price).toFixed(2)}`;

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function updateOrderType(type) {
            currentOrderType = type;
            console.log('Order type updated to:', type);
        }

        // CATEGORY AND PRODUCT CLICK HANDLERS
        document.addEventListener('click', function (e) {
            // Check if clicked element is a category item or inside one
            const categoryItem = e.target.closest('.category-item');

            if (categoryItem) {
                // Remove active class from all items
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
                // Add active class to clicked item
                categoryItem.classList.add('active');

                // Hide category prompt
                const categoryPrompt = document.getElementById('categoryPrompt');
                if (categoryPrompt) {
                    categoryPrompt.style.display = 'none';
                }

                const categoryId = categoryItem.dataset.categoryId;
                const category = categoryItem.dataset.category;

                console.log('Category clicked:', category, 'ID:', categoryId);

                // Hide all products first
                document.querySelectorAll('.product-card').forEach(card => {
                    card.classList.remove('show');
                    card.classList.add('hide');
                    card.style.display = 'none';
                });

                // Show products for selected category
                const categoryProducts = document.querySelectorAll(`.product-card[data-category="${category}"]`);
                console.log('Found products for category:', categoryProducts.length);

                categoryProducts.forEach(card => {
                    card.classList.remove('hide');
                    card.classList.add('show');
                    card.style.display = 'block';
                });

                // If no products found, try fetching from server
                if (categoryProducts.length === 0 && categoryId) {
                    fetch(`/category/${categoryId}/items`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched items:', data);
                            menuItems = data.menuItems;
                            updateProductsGrid(data.menuItems);
                        })
                        .catch(error => {
                            console.error('Error fetching category items:', error);
                        });
                }
                return;
            }

            // Handle product card clicks (only if not clicking on a category)
            const productCard = e.target.closest('.product-card');
            if (productCard) {
                console.log('Product card clicked!');

                const itemId = parseInt(productCard.dataset.id);
                const hasVariants = productCard.dataset.hasVariants === 'true';
                const menuItem = menuItems.find(item => item.id === itemId);

                console.log('Menu item found:', menuItem);
                console.log('Has variants:', hasVariants);

                if (!menuItem) {
                    console.log('No menu item found for ID:', itemId);
                    return;
                }

                // Check if item has variants
                if (hasVariants && menuItem.variants && menuItem.variants.length > 0) {
                    console.log('Variants not implemented yet');
                } else {
                    const menuItemDiv = productCard.closest('.menu-item');
                    if (menuItemDiv) {
                        openItemModalFromData(menuItemDiv);
                    }
                }

                // Add click animation
                productCard.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    productCard.style.transform = '';
                }, 150);
            }

            // Handle modal clicks
            const modal = e.target.closest('#itemDetailsModal');
            if (modal && e.target === modal) {
                closeItemModal();
            }

            // Handle addon modal clicks
            const addonModal = e.target.closest('#addonModal');
            if (addonModal && e.target === addonModal) {
                closeAddonModal();
            }
        });

        function updateProductsGrid(items) {
            const productsGrid = document.getElementById('productsGrid');
            if (!productsGrid) return;

            // Keep the category prompt but clear products
            const categoryPrompt = document.getElementById('categoryPrompt');
            productsGrid.innerHTML = '';
            if (categoryPrompt) {
                productsGrid.appendChild(categoryPrompt);
                categoryPrompt.style.display = 'none';
            }

            items.forEach(item => {
                const menuItemDiv = document.createElement('div');
                menuItemDiv.className = 'menu-item cursor-pointer';
                menuItemDiv.dataset.name = item.name;
                menuItemDiv.dataset.price = parseFloat(item.price).toFixed(2);

                // Ensure proper image URL with full path
                const fullImageUrl = item.image ?
                    (item.image.startsWith('/assets/') ? item.image : `/assets/${item.image}`) : '';
                menuItemDiv.dataset.image = fullImageUrl;
                menuItemDiv.dataset.description = item.description || '';

                const productCard = document.createElement('div');
                productCard.className = 'product-card show';
                productCard.dataset.category = item.category ? item.category.name.toLowerCase().replace(' ', '_') : '';
                productCard.dataset.id = item.id;
                productCard.dataset.hasVariants = item.has_variants ? 'true' : 'false';

                const imageUrl = fullImageUrl ||
                    "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'><rect width='200' height='150' fill='%23F5F5F5'/><circle cx='100' cy='75' r='40' fill='%238B4513'/><circle cx='100' cy='65' r='25' fill='%23D2691E'/><circle cx='100' cy='55' r='15' fill='%23F4A460'/></svg>";

                productCard.innerHTML = `
            <img src="${imageUrl}" alt="${item.name}" class="product-image">
            <div class="product-info">
                <h3 class="product-name">${item.name}</h3>
                <p class="product-price">PHP ${parseFloat(item.price).toFixed(2)}</p>
                ${item.description ? `<p class="product-description" style="font-size: 0.8rem; color: #666; margin-top: 4px;">${item.description.substring(0, 50)}${item.description.length > 50 ? '...' : ''}</p>` : ''}
            </div>
        `;

                menuItemDiv.appendChild(productCard);
                productsGrid.appendChild(menuItemDiv);
            });
        }

        function addToCart(menuItem, variant = null, customizations = {}) {
            if (!menuItem) {
                console.error('No menu item provided to addToCart');
                return;
            }

            const itemName = variant ? `${menuItem.name} (${variant.name})` : menuItem.name;
            let itemPrice = variant ? variant.price : parseFloat(menuItem.price);

            // Add addon prices
            const addonsPrice = customizations.addons ?
                customizations.addons.reduce((total, addon) => total + addon.price, 0) : 0;

            if (customizations.sizeAdjustment) {
                itemPrice += customizations.sizeAdjustment;
            }

            const itemId = menuItem.id || Date.now();
            const variantId = variant ? variant.id : null;
            const quantity = customizations.quantity || currentQuantity || 1;

            // Store the complete image URL with the cart item
            const itemImage = menuItem.image || '';
            console.log('Adding to cart - Item image:', itemImage); // Debug log

            // Create unique cart key including addons
            const addonKeys = customizations.addons ?
                customizations.addons.map(addon => addon.name).sort().join(',') : '';
            const cartKey = `${itemId}_${variantId || 'none'}_${customizations.size || 'medium'}_${addonKeys}`;

            const existingItemIndex = cart.findIndex(item => item.cartKey === cartKey);

            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity += quantity;
            } else {
                cart.push({
                    cartKey: cartKey,
                    menu_item_id: itemId,
                    variant_id: variantId,
                    name: itemName,
                    price: itemPrice,
                    image: itemImage, // Store complete image URL
                    addons: customizations.addons || [],
                    addonsPrice: addonsPrice,
                    quantity: quantity,
                    size: customizations.size || 'medium',
                    sizeAdjustment: customizations.sizeAdjustment || 0
                });
            }

            console.log('Current cart:', cart); // Debug log
            updateTotal();
            updateCartDisplay();
            closeItemModal();
        }

        function updateTotal() {
            total = cart.reduce((sum, item) => {
                const itemTotal = (item.price + item.addonsPrice) * item.quantity;
                return sum + itemTotal;
            }, 0);

            const totalAmountEl = document.getElementById('totalAmount');
            if (totalAmountEl) totalAmountEl.textContent = `PHP ${total.toFixed(2)}`;

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const closeBtn = document.getElementById('closeCartBtn');
            const cartSection = document.querySelector('.cart-section');

            if (closeBtn && cartSection) {
                if (totalItems > 0) {
                    if (cartSection.classList.contains('minimized')) {
                        closeBtn.innerHTML = `Λ SHOW CART (${totalItems})`;
                    } else {
                        closeBtn.innerHTML = `V HIDE CART (${totalItems})`;
                    }
                } else {
                    if (cartSection.classList.contains('minimized')) {
                        closeBtn.textContent = 'Λ SHOW CART';
                    } else {
                        closeBtn.textContent = 'V HIDE CART';
                    }
                }
            }
        }

        // Checkout functionality - UPDATED
        document.getElementById('checkoutBtn')?.addEventListener('click', function () {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            // Store cart in session and redirect to review order page
            fetch('/kiosk/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    order_type: currentOrderType,
                    items: cart,
                    total: total,
                    subtotal: total,
                    tax_amount: 0,
                    discount_amount: 0
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to review order page instead of payment
                        window.location.href = '/kiosk/review-order';
                    } else {
                        alert('Error processing checkout: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Checkout error:', error);
                    alert('Error processing checkout. Please try again.');
                });
        });

        // Cancel button functionality
        document.getElementById('cancelBtn')?.addEventListener('click', function () {
            const cancelModal = document.getElementById('cancelModal');
            if (cancelModal) cancelModal.style.display = 'flex';
        });

        document.getElementById('confirmYes')?.addEventListener('click', function () {
            cart = [];
            total = 0;
            updateTotal();
            updateCartDisplay();
            window.location.href = '/kiosk';
        });

        document.getElementById('confirmNo')?.addEventListener('click', function () {
            const cancelModal = document.getElementById('cancelModal');
            if (cancelModal) cancelModal.style.display = 'none';
        });

        document.getElementById('cancelModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Cart toggle functionality
        document.getElementById('closeCartBtn')?.addEventListener('click', function () {
            const cartSection = document.querySelector('.cart-section');
            const closeBtn = this;
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

            if (cartSection) {
                if (cartSection.classList.contains('minimized')) {
                    cartSection.classList.remove('minimized');
                    closeBtn.innerHTML = totalItems > 0 ? `V HIDE CART (${totalItems})` : 'V HIDE CART';
                } else {
                    cartSection.classList.add('minimized');
                    closeBtn.innerHTML = totalItems > 0 ? `Λ SHOW CART (${totalItems})` : 'Λ SHOW CART';
                }
            }
        });
    </script>
</body>

</html><?php /**PATH C:\Users\Laurence Ayo\sip_and_serve_final\resources\views/kioskMain.blade.php ENDPATH**/ ?>