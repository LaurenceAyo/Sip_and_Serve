<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ThermalPrinterService
{
    private $printerName;
    private $printerType;
    private $printerPath;

    public function __construct()
    {
        $this->printerName = env('THERMAL_PRINTER_NAME', 'POS58 Printer');
        $this->printerType = env('THERMAL_PRINTER_TYPE', 'windows');
        $this->printerPath = env('THERMAL_PRINTER_PATH', 'POS58 Printer');
    }

    public function printReceipt($order)
    {
        try {
            Log::info('=== STARTING THERMAL PRINTER RECEIPT GENERATION ===', [
                'order_id' => $order->id,
                'printer_name' => $this->printerName,
                'printer_path' => $this->printerPath
            ]);

            // Create printer connector with enhanced error handling
            $connector = $this->createPrinterConnector();
            
            if (!$connector) {
                throw new Exception('Failed to create printer connector - see logs for details');
            }

            Log::info('Printer connector created successfully');

            // Create printer instance with specific POS58 settings
            $printer = new Printer($connector);
            Log::info('Printer instance created successfully');

            // Generate and print receipt with POS58 optimizations
            $this->generatePOS58Receipt($printer, $order);
            Log::info('Receipt content sent to printer');

            // Close printer connection
            $printer->close();
            Log::info('Printer connection closed');

            // Try to open cash drawer
            $this->openCashDrawer();

            Log::info('=== THERMAL RECEIPT PRINTED SUCCESSFULLY ===');
            return true;

        } catch (Exception $e) {
            Log::error('=== THERMAL PRINTER ERROR ===', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'printer_config' => [
                    'name' => $this->printerName,
                    'type' => $this->printerType,
                    'path' => $this->printerPath
                ]
            ]);

            // Save receipt to file for debugging
            $this->saveReceiptToFile($order);
            
            return false;
        }
    }

    private function createPrinterConnector()
    {
        $printerNames = [
            $this->printerPath,
            $this->printerName,
            'POS58 Printer',
            'POS-58',
            'GoojPRT PT-210',
            'GoojPRT',
            'Generic / Text Only',
            'USB001'
        ];

        foreach ($printerNames as $printerName) {
            try {
                Log::info("Attempting to connect to printer: {$printerName}");
                
                // Create connector
                $connector = new WindowsPrintConnector($printerName);
                
                // Test the connection by creating a printer instance
                $testPrinter = new Printer($connector);
                
                // Send a simple test command to verify connection
                $testPrinter->initialize();
                $testPrinter->close();
                
                Log::info("SUCCESS: Connected to printer: {$printerName}");
                return new WindowsPrintConnector($printerName);
                
            } catch (Exception $e) {
                Log::debug("Failed to connect to printer {$printerName}: " . $e->getMessage());
                continue;
            }
        }

        Log::error('All printer connection attempts failed');
        
        // Fallback to file connector for debugging
        return $this->createFileConnector();
    }

    private function generatePOS58Receipt(Printer $printer, $order)
    {
        try {
            Log::info('Generating POS58 optimized receipt');

            // Initialize printer with POS58 specific settings
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            
            // Header - optimized for 58mm thermal printers
            $printer->setTextSize(1, 2);
            $printer->text("SIP & SERVE CAFE\n");
            $printer->setTextSize(1, 1);
            $printer->text("Official Receipt\n");
            $printer->text("========================\n");
            $printer->feed();

            // Order details - 24 characters wide for 58mm
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Receipt: " . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)) . "\n");
            $printer->text("Date: " . $order->created_at->format('M d, Y H:i') . "\n");
            $printer->text("Cashier: " . (Auth::user()->name ?? 'System') . "\n");
            $printer->text("Type: " . ucfirst($order->order_type ?? 'Dine-in') . "\n");
            
            if ($order->customer_name) {
                $printer->text("Customer: " . substr($order->customer_name, 0, 16) . "\n");
            }
            
            $printer->feed();
            $printer->text("------------------------\n");
            $printer->text("ITEMS:\n");
            $printer->text("------------------------\n");

            // Order items - formatted for 58mm width
            $calculatedSubtotal = 0;
            foreach ($order->orderItems as $item) {
                $itemName = $item->name ?? $item->menuItem->name ?? 'Custom Item';
                $quantity = (int) $item->quantity;
                
                // Calculate unit price correctly
                $unitPrice = 0;
                if ($item->unit_price && $item->unit_price > 0) {
                    $unitPrice = (float) $item->unit_price;
                } elseif ($item->total_price && $quantity > 0) {
                    $unitPrice = (float) $item->total_price / $quantity;
                } elseif ($item->menuItem && $item->menuItem->price) {
                    $unitPrice = (float) $item->menuItem->price;
                }
                
                $totalPrice = $unitPrice * $quantity;
                $calculatedSubtotal += $totalPrice;

                // Format for 58mm printer (24 characters)
                $truncatedName = substr($itemName, 0, 20);
                $printer->text($truncatedName . "\n");
                $printer->text(sprintf("  %dx%.2f = %.2f\n", 
                    $quantity, 
                    $unitPrice, 
                    $totalPrice
                ));
            }

            $printer->feed();
            $printer->text("------------------------\n");

            // Totals
            $taxAmount = is_numeric($order->tax_amount) ? (float) $order->tax_amount : 0;
            $discountAmount = is_numeric($order->discount_amount) ? (float) $order->discount_amount : 0;
            $totalAmount = $calculatedSubtotal + $taxAmount - $discountAmount;
            $cashAmount = is_numeric($order->cash_amount) ? (float) $order->cash_amount : 0;
            $changeAmount = is_numeric($order->change_amount) ? (float) $order->change_amount : 0;

            $printer->text(sprintf("Subtotal: %.2f\n", $calculatedSubtotal));
            
            if ($taxAmount > 0) {
                $printer->text(sprintf("VAT 12%%: %.2f\n", $taxAmount));
            }
            
            if ($discountAmount > 0) {
                $printer->text(sprintf("Discount: -%.2f\n", $discountAmount));
            }

            $printer->feed();
            $printer->text("========================\n");
            $printer->setTextSize(1, 2);
            $printer->text(sprintf("TOTAL: %.2f\n", $totalAmount));
            $printer->setTextSize(1, 1);
            $printer->text("========================\n");
            $printer->feed();

            // Payment details
            $printer->text("PAYMENT DETAILS:\n");
            $printer->text("------------------------\n");
            $printer->text("Payment: CASH\n");
            $printer->text(sprintf("Cash: %.2f\n", $cashAmount));
            
            if ($changeAmount > 0) {
                $printer->text(sprintf("Change: %.2f\n", $changeAmount));
            }
            
            $printer->text("Status: PAID\n");
            $printer->feed();
            $printer->text("========================\n");

            // Special instructions
            if ($order->special_instructions) {
                $printer->text("Instructions:\n");
                $lines = explode("\n", wordwrap($order->special_instructions, 22, "\n", true));
                foreach ($lines as $line) {
                    $printer->text($line . "\n");
                }
                $printer->text("------------------------\n");
                $printer->feed();
            }
            
            // Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed();
            $printer->text("Thank you for dining\n");
            $printer->text("with us!\n");
            $printer->text("Please come again!\n");
            $printer->feed();
            $printer->text("========================\n");
            $printer->text("BIR: 2819550\n");
            $printer->text("TIN: 269-004-339-000-00\n");
            $printer->text("www.sipandserve.com\n");
            $printer->text("========================\n");
            
            // Multiple feed and cut attempts for POS58
            $printer->feed(3);
            
            // Try different cut commands for POS58
            try {
                // Standard cut
                $printer->cut();
                Log::info('Standard cut command sent');
            } catch (Exception $e) {
                try {
                    // Partial cut
                    $printer->cut(Printer::CUT_PARTIAL);
                    Log::info('Partial cut command sent');
                } catch (Exception $e2) {
                    try {
                        // Manual cut command for POS58
                        $printer->getPrintConnector()->write("\x1D\x56\x00");
                        Log::info('Manual cut command sent');
                    } catch (Exception $e3) {
                        Log::debug('All cut commands failed, using feed');
                        $printer->feed(4);
                    }
                }
            }

            Log::info('POS58 receipt generation completed');

        } catch (Exception $e) {
            Log::error('Error generating POS58 receipt', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            throw $e;
        }
    }

    private function openCashDrawer()
    {
        try {
            Log::info('Attempting to open cash drawer via POS58');

            $connector = new WindowsPrintConnector($this->printerPath);
            $printer = new Printer($connector);
            
            // Multiple cash drawer commands for different drawer types
            $cashDrawerCommands = [
                "\x1B\x70\x00\x19\xFA", // Standard ESC/POS
                "\x1B\x70\x00\x32\x32", // Alternative timing
                "\x1B\x70\x01\x19\xFA", // Drawer 2
            ];

            foreach ($cashDrawerCommands as $command) {
                try {
                    $printer->getPrintConnector()->write($command);
                    Log::info('Cash drawer command sent: ' . bin2hex($command));
                    break; // If successful, stop trying other commands
                } catch (Exception $e) {
                    Log::debug('Cash drawer command failed: ' . $e->getMessage());
                    continue;
                }
            }
            
            $printer->close();
            Log::info('Cash drawer commands completed');
            
        } catch (Exception $e) {
            Log::error('Failed to open cash drawer', [
                'error' => $e->getMessage()
            ]);
        }
    }

    private function createFileConnector()
    {
        $receiptPath = storage_path('app/thermal_receipts');
        if (!file_exists($receiptPath)) {
            mkdir($receiptPath, 0755, true);
        }
        
        $filename = 'receipt_' . time() . '_order_' . (request('order_id') ?? 'unknown') . '.txt';
        $fullPath = $receiptPath . '/' . $filename;
        
        Log::info('Creating file connector for debugging', [
            'path' => $fullPath
        ]);
        
        return new FilePrintConnector($fullPath);
    }

    private function saveReceiptToFile($order)
    {
        try {
            $receiptPath = storage_path('app/receipts');
            if (!file_exists($receiptPath)) {
                mkdir($receiptPath, 0755, true);
            }

            $receiptFile = $receiptPath . '/receipt_' . $order->id . '_' . now()->format('YmdHis') . '.txt';
            $receiptContent = $this->generateTextReceipt($order);
            file_put_contents($receiptFile, $receiptContent);
            
            Log::info('Receipt saved to file as fallback', [
                'order_id' => $order->id,
                'file_path' => $receiptFile
            ]);
        } catch (Exception $e) {
            Log::error('Failed to save receipt file', [
                'error' => $e->getMessage()
            ]);
        }
    }

    private function generateTextReceipt($order)
    {
        $receipt = "";
        $receipt .= "========================\n";
        $receipt .= "   SIP & SERVE CAFE     \n";
        $receipt .= "========================\n";
        $receipt .= "    OFFICIAL RECEIPT    \n";
        $receipt .= "========================\n\n";

        $receipt .= "Receipt: " . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)) . "\n";
        $receipt .= "Date: " . $order->created_at->format('M d, Y H:i') . "\n";
        $receipt .= "Cashier: " . (Auth::user()->name ?? 'System') . "\n";
        $receipt .= "Printer: " . $this->printerPath . "\n\n";

        return $receipt;
    }

    public function testPrinter()
    {
        try {
            Log::info('=== TESTING POS58 THERMAL PRINTER ===');
            
            $connector = $this->createPrinterConnector();
            
            if (!$connector) {
                throw new Exception('Could not create connector');
            }
            
            $printer = new Printer($connector);
            
            // Send a more comprehensive test
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("=== PRINTER TEST ===\n");
            $printer->text("POS58 Connection Test\n");
            $printer->text("Time: " . now()->format('H:i:s') . "\n");
            $printer->text("Date: " . now()->format('Y-m-d') . "\n");
            $printer->text("Printer: " . $this->printerPath . "\n");
            $printer->text("====================\n");
            $printer->feed(2);
            
            // Test different formatting
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Left aligned text\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Center aligned\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Right aligned\n");
            
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed(2);
            $printer->text("TEST COMPLETE\n");
            $printer->feed(3);
            
            // Try to cut
            try {
                $printer->cut();
            } catch (Exception $e) {
                $printer->feed(2);
            }
            
            $printer->close();
            
            Log::info('=== POS58 PRINTER TEST COMPLETED SUCCESSFULLY ===');
            return true;
        } catch (Exception $e) {
            Log::error('=== POS58 PRINTER TEST FAILED ===', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getConnectionInfo()
    {
        return [
            'configured_printer_name' => $this->printerName,
            'configured_printer_path' => $this->printerPath,
            'printer_type' => 'POS58 Thermal Printer',
            'os_family' => PHP_OS_FAMILY,
            'timestamp' => now()->toISOString()
        ];
    }
}