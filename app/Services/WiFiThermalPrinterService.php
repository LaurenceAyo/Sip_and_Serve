<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class WiFiThermalPrinterService
{
    protected $printerIP;
    protected $printerPort;
    protected $timeout;
    protected $debugMode;

    public function __construct()
    {
        $this->printerIP = env('WIFI_PRINTER_IP', '192.168.1.100');
        $this->printerPort = env('WIFI_PRINTER_PORT', 9100);
        $this->timeout = env('WIFI_PRINTER_TIMEOUT', 5);
        $this->debugMode = env('WIFI_PRINTER_DEBUG', false);
    }

    /**
     * Print receipt via WiFi
     */
    public function printReceipt(Order $order)
    {
        try {
            Log::info('WiFi Printer - Print receipt request', [
                'order_id' => $order->id,
                'printer_ip' => $this->printerIP
            ]);

            // Test connection first
            if (!$this->testConnection()) {
                throw new Exception('Printer not reachable at ' . $this->printerIP);
            }

            // Generate receipt content
            $receiptContent = $this->generateReceiptContent($order);
            
            // Convert to ESC/POS commands
            $escposData = $this->createESCPOSCommands($receiptContent);
            
            // Send to printer via HTTP
            return $this->sendToPrinter($escposData);

        } catch (Exception $e) {
            Log::error('WiFi Printer - Receipt printing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Test printer connection
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("http://{$this->printerIP}:{$this->printerPort}/status");
            
            return $response->successful();
        } catch (Exception $e) {
            // Try alternative connection test
            return $this->testRawConnection();
        }
    }

    /**
     * Alternative connection test using socket
     */
    private function testRawConnection()
    {
        try {
            $socket = @fsockopen($this->printerIP, $this->printerPort, $errno, $errstr, $this->timeout);
            if ($socket) {
                fclose($socket);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Send ESC/POS data to printer
     */
    private function sendToPrinter($escposData)
    {
        try {
            // Method 1: HTTP POST (if printer supports web interface)
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/octet-stream'])
                ->withBody($escposData)
                ->post("http://{$this->printerIP}:{$this->printerPort}/print");

            if ($response->successful()) {
                Log::info('WiFi Printer - Print successful via HTTP');
                return true;
            }

            // Method 2: Raw socket connection
            return $this->sendViaSocket($escposData);

        } catch (Exception $e) {
            Log::error('WiFi Printer - HTTP print failed, trying socket', [
                'error' => $e->getMessage()
            ]);
            return $this->sendViaSocket($escposData);
        }
    }

    /**
     * Send data via raw socket connection
     */
    private function sendViaSocket($escposData)
    {
        try {
            $socket = fsockopen($this->printerIP, $this->printerPort, $errno, $errstr, $this->timeout);
            
            if (!$socket) {
                throw new Exception("Socket connection failed: $errstr ($errno)");
            }

            fwrite($socket, $escposData);
            fclose($socket);

            Log::info('WiFi Printer - Print successful via socket');
            return true;

        } catch (Exception $e) {
            Log::error('WiFi Printer - Socket print failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate receipt content
     */
    private function generateReceiptContent(Order $order)
    {
        $content = str_repeat('=', 32) . "\n";
        $content .= $this->centerText('RECEIPT', 32) . "\n";
        $content .= str_repeat('=', 32) . "\n";
        $content .= "Date: " . $order->created_at->format('Y-m-d H:i:s') . "\n";
        $content .= "Order: #" . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)) . "\n";
        $content .= "Type: " . ucfirst($order->order_type ?? 'dine-in') . "\n";
        
        if ($order->customer_name) {
            $content .= "Customer: " . $order->customer_name . "\n";
        }
        
        $content .= "\n";

        // Order items
        foreach ($order->orderItems as $item) {
            $name = $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Unknown Item');
            $quantity = $item->quantity;
            $unitPrice = $item->unit_price ?? ($item->menuItem ? $item->menuItem->price : 0);
            $totalPrice = $item->total_price ?? ($unitPrice * $quantity);

            $content .= sprintf("%-20s %2dx\n", $this->truncateText($name, 20), $quantity);
            $content .= sprintf("%20s $%6.2f\n", "@$" . number_format($unitPrice, 2), $totalPrice);
        }

        $content .= str_repeat('-', 32) . "\n";
        $content .= sprintf("%-20s $%6.2f\n", "Subtotal:", $order->subtotal ?? $order->total_amount);
        
        if ($order->tax_amount && $order->tax_amount > 0) {
            $content .= sprintf("%-20s $%6.2f\n", "Tax:", $order->tax_amount);
        }
        
        $content .= sprintf("%-20s $%6.2f\n", "TOTAL:", $order->total_amount);

        if ($order->cash_amount && $order->cash_amount > 0) {
            $content .= sprintf("%-20s $%6.2f\n", "Cash Received:", $order->cash_amount);
            $content .= sprintf("%-20s $%6.2f\n", "Change:", $order->change_amount ?? 0);
        }

        $content .= "\n";
        
        if ($order->special_instructions) {
            $content .= "Special Instructions:\n";
            $content .= wordwrap($order->special_instructions, 32) . "\n\n";
        }

        $content .= $this->centerText('Thank you!', 32) . "\n";
        $content .= str_repeat('=', 32) . "\n";

        return $content;
    }

    /**
     * Create ESC/POS commands for GOOJPRT PT-210
     */
    private function createESCPOSCommands($text)
    {
        $commands = [];
        
        // Initialize printer
        $commands[] = chr(27) . chr(64); // ESC @
        
        // Set character set to PC437 (USA)
        $commands[] = chr(27) . chr(116) . chr(0); // ESC t 0
        
        // Set character spacing
        $commands[] = chr(27) . chr(32) . chr(0); // ESC SP 0
        
        // Set line spacing
        $commands[] = chr(27) . chr(51) . chr(32); // ESC 3 32
        
        // Add text content
        $commands[] = $text;
        
        // Feed lines
        $commands[] = chr(27) . chr(100) . chr(5); // ESC d 5 (feed 5 lines)
        
        // Cut paper (full cut)
        $commands[] = chr(29) . chr(86) . chr(65) . chr(0); // GS V A 0
        
        return implode('', $commands);
    }

    /**
     * Test printer with sample receipt
     */
    public function testPrinter()
    {
        try {
            Log::info('WiFi Printer - Test print requested');

            if (!$this->testConnection()) {
                throw new Exception('Printer not reachable');
            }

            $testContent = str_repeat('=', 32) . "\n";
            $testContent .= $this->centerText('TEST PRINT', 32) . "\n";
            $testContent .= str_repeat('=', 32) . "\n";
            $testContent .= "Date: " . now()->format('Y-m-d H:i:s') . "\n";
            $testContent .= "Printer IP: " . $this->printerIP . "\n";
            $testContent .= "Type: WiFi ESC/POS\n";
            $testContent .= "\n";
            $testContent .= "Connection test successful!\n";
            $testContent .= "GOOJPRT PT-210 Ready\n";
            $testContent .= "\n";
            $testContent .= str_repeat('=', 32) . "\n";

            $escposData = $this->createESCPOSCommands($testContent);
            return $this->sendToPrinter($escposData);

        } catch (Exception $e) {
            Log::error('WiFi Printer - Test print failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get printer status information
     */
    public function getStatus()
    {
        $connected = $this->testConnection();
        
        return [
            'connected' => $connected,
            'printer_ip' => $this->printerIP,
            'printer_port' => $this->printerPort,
            'printer_type' => 'WiFi ESC/POS',
            'model' => 'GOOJPRT PT-210',
            'connection_method' => 'TCP/IP Socket',
            'status' => $connected ? 'Ready' : 'Disconnected',
            'last_checked' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Helper method to center text
     */
    private function centerText($text, $width)
    {
        $padding = ($width - strlen($text)) / 2;
        return str_repeat(' ', floor($padding)) . $text . str_repeat(' ', ceil($padding));
    }

    /**
     * Helper method to truncate text
     */
    private function truncateText($text, $maxLength)
    {
        return strlen($text) > $maxLength ? substr($text, 0, $maxLength - 3) . '...' : $text;
    }
}