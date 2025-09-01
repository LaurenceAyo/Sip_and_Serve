<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MenuItem;
use App\Services\PaymongoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRPaymentController extends Controller
{
    private $paymongoService;

    public function __construct()
    {
        try {
            $this->paymongoService = app(PaymongoService::class);
        } catch (\Exception $e) {
            Log::warning('PaymongoService not available', ['error' => $e->getMessage()]);
            $this->paymongoService = null;
        }
    }

    public function showQRPaymentPage($orderId)
    {
        if ($orderId === 'new') {
            return $this->createNewOrder();
        }

        $order = Order::with('orderItems.menuItem')->findOrFail($orderId);
        return view('qr-payment-page', compact('order'));
    }

    private function createNewOrder()
    {
        try {
            $orderType = request('type', 'dine-in');
            $items = json_decode(request('items', '[]'), true);

            if (empty($items)) {
                return redirect()->route('kiosk.index')->with('error', 'No items in cart');
            }

            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                if (!isset($item['menu_item_id'], $item['quantity'])) continue;

                $menuItem = MenuItem::find($item['menu_item_id']);
                if (!$menuItem) continue;

                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $menuItem->price;
                $totalPrice = $quantity * $unitPrice;
                
                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'menu_variant_id' => $item['menu_variant_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'status' => 'pending'
                ];

                $subtotal += $totalPrice;
            }

            if (empty($orderItems) || $subtotal <= 0) {
                return redirect()->route('kiosk.index')->with('error', 'Invalid order');
            }

            DB::beginTransaction();
            
            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total_amount' => $subtotal,
                'payment_method' => 'qr',
                'payment_status' => 'pending',
                'status' => 'pending',
                'order_type' => $orderType,
                'notes' => "QR Payment - {$orderType}"
            ]);

            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }

            DB::commit();

            Log::info('QR order created', [
                'order_id' => $order->id,
                'total' => $order->total_amount,
                'type' => $orderType
            ]);

            return view('qr-payment-page', compact('order'));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('QR order creation failed', ['error' => $e->getMessage()]);
            return redirect()->route('kiosk.index')->with('error', 'Order creation failed');
        }
    }

    public function generateOrderQR($orderId)
    {
        Log::info('QR generation started', ['order_id' => $orderId]);
        
        try {
            $order = Order::findOrFail($orderId);
            
            // Create payment data string for QR code
            $paymentData = [
                'merchant' => "L' Primero Cafe",
                'amount' => $order->total_amount,
                'order_id' => $order->id,
                'gcash_number' => '09123456789', // Your actual GCash number
                'reference' => "ORDER-{$order->id}"
            ];

            // Create GCash-compatible QR code content
            $qrContent = json_encode([
                'v' => '1',
                'mode' => 'SEND_MONEY',
                'recipient' => $paymentData['gcash_number'],
                'amount' => $order->total_amount,
                'message' => "L' Primero Cafe Order #{$order->id}",
                'reference' => "ORDER-{$order->id}"
            ]);

            // Generate QR code as base64 image
            $qrCode = new QrCode($qrContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

            Log::info('QR code generated successfully', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'qr_image' => $qrBase64,
                'qr_content' => $qrContent,
                'payment_data' => $paymentData,
                'order_total' => number_format((float) $order->total_amount, 2),
                'manual_payment' => false
            ]);

        } catch (\Exception $e) {
            Log::error('QR generation failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'QR code generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            // Check if payment is already confirmed
            $isPaid = $order->payment_status === 'paid';
            
            Log::info('Payment status checked', [
                'order_id' => $orderId,
                'status' => $order->payment_status,
                'paid' => $isPaid
            ]);

            return response()->json([
                'paid' => $isPaid,
                'status' => $order->payment_status,
                'order_status' => $order->status,
                'amount' => $order->total_amount
            ]);

        } catch (\Exception $e) {
            Log::error('Status check failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'paid' => false,
                'error' => 'Status check failed'
            ], 500);
        }
    }

    public function confirmPayment($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now()
            ]);

            Log::info('Payment confirmed manually', ['order_id' => $orderId]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Confirmation failed'
            ], 500);
        }
    }

    public function generateStaticQR()
    {
        try {
            $businessInfo = [
                'merchant_name' => "L' Primero Cafe",
                'gcash_number' => '09123456789',
                'instructions' => 'Send payment and show receipt to staff'
            ];

            $qrContent = "GCash Payment\nMerchant: {$businessInfo['merchant_name']}\nNumber: {$businessInfo['gcash_number']}\nInstructions: {$businessInfo['instructions']}";

            $qrCode = new QrCode($qrContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

            return response()->json([
                'success' => true,
                'qr_image' => $qrBase64,
                'business_info' => $businessInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Static QR generation failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'QR generation failed'
            ], 500);
        }
    }
}