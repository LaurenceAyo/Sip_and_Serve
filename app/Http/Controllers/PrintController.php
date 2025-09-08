<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ThermalPrinterService;
use App\Models\Order; // Adjust based on your Order model
use Illuminate\Support\Facades\Log;

class PrintController extends Controller
{
    private $printerService;
    
    public function __construct(ThermalPrinterService $printerService)
    {
        $this->printerService = $printerService;
    }
    
    public function printReceipt(Request $request)
    {
        try {
            Log::info('Print receipt request received', $request->all());
            
            // Get the order
            $orderId = $request->input('order_id');
            $order = Order::with('orderItems.menuItem')->findOrFail($orderId);
            
            // Print the receipt
            $success = $this->printerService->printReceipt($order);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Receipt printed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to print receipt. Check printer connection.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Print receipt error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function testPrinter()
    {
        try {
            $success = $this->printerService->testPrinter();
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Printer test successful' : 'Printer test failed',
                'connection_info' => $this->printerService->getConnectionInfo()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}