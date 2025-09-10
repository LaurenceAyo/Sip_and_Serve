<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class CashDrawerService
{
    protected $comPort;
    protected $baudRate;
    protected $drawerType;

    public function __construct()
    {
        $this->comPort = env('CASH_DRAWER_COM_PORT', 'COM3'); // Windows COM port
        $this->baudRate = env('CASH_DRAWER_BAUD_RATE', 9600);
        $this->drawerType = env('CASH_DRAWER_TYPE', 'serial'); // 'serial', 'usb', 'arduino'
    }

    /**
     * Open cash drawer via serial/USB adapter
     */
    public function openDrawer($drawerNumber = 1)
    {
        try {
            Log::info('Cash Drawer - Opening drawer via adapter', [
                'drawer_number' => $drawerNumber,
                'com_port' => $this->comPort,
                'type' => $this->drawerType
            ]);

            switch ($this->drawerType) {
                case 'serial':
                    return $this->openDrawerSerial();
                case 'arduino':
                    return $this->openDrawerArduino();
                case 'usb_relay':
                    return $this->openDrawerUSBRelay();
                case 'windows_cmd':
                    return $this->openDrawerWindowsCommand();
                default:
                    return $this->openDrawerSerial();
            }

        } catch (Exception $e) {
            Log::error('Cash Drawer - Error opening drawer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Method 1: Serial Port Communication (most common)
     */
    private function openDrawerSerial()
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows COM port approach
                $handle = fopen($this->comPort, 'r+b');
                if ($handle === false) {
                    throw new Exception("Could not open COM port: " . $this->comPort);
                }

                // Send drawer open signal (common cash drawer signal)
                $signal = chr(7); // Bell character - triggers many cash drawers
                fwrite($handle, $signal);
                fflush($handle);
                
                // Alternative signals to try:
                // fwrite($handle, chr(20)); // DC4 character
                // fwrite($handle, "\x10\x14\x01\x00\x05"); // Standard cash drawer command
                
                fclose($handle);

                Log::info('Cash Drawer - Serial signal sent successfully', [
                    'port' => $this->comPort,
                    'signal' => 'chr(7)'
                ]);

                return true;

            } else {
                // Linux/Mac serial port approach
                $device = '/dev/ttyUSB0'; // or /dev/ttyACM0 for Arduino
                if (file_exists($device)) {
                    exec("echo -ne '\007' > $device", $output, $returnCode);
                    
                    if ($returnCode === 0) {
                        Log::info('Cash Drawer - Linux serial signal sent', ['device' => $device]);
                        return true;
                    }
                }
                throw new Exception("Could not access serial device: $device");
            }

        } catch (Exception $e) {
            Log::error('Cash Drawer - Serial communication failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Method 2: Arduino-based cash drawer controller
     */
    private function openDrawerArduino()
    {
        try {
            $command = json_encode([
                'action' => 'open_drawer',
                'drawer_id' => 1,
                'pulse_duration' => 500 // milliseconds
            ]);

            if (PHP_OS_FAMILY === 'Windows') {
                $handle = fopen($this->comPort, 'r+b');
                if ($handle === false) {
                    throw new Exception("Could not open Arduino port: " . $this->comPort);
                }

                fwrite($handle, $command . "\n");
                fflush($handle);
                
                // Wait for Arduino response
                sleep(1);
                $response = fgets($handle);
                fclose($handle);

                Log::info('Cash Drawer - Arduino command sent', [
                    'command' => $command,
                    'response' => trim($response)
                ]);

                return strpos($response, 'OK') !== false;

            } else {
                $device = '/dev/ttyACM0'; // Arduino typically uses ACM
                if (file_exists($device)) {
                    file_put_contents($device, $command . "\n");
                    Log::info('Cash Drawer - Arduino Linux command sent', ['device' => $device]);
                    return true;
                }
                throw new Exception("Arduino device not found: $device");
            }

        } catch (Exception $e) {
            Log::error('Cash Drawer - Arduino communication failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Method 3: USB Relay Module
     */
    private function openDrawerUSBRelay()
    {
        try {
            // For USB relay modules like HID-based ones
            $relayCommand = env('CASH_DRAWER_RELAY_COMMAND', 'relay_control.exe');
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Example for common USB relay controllers
                $cmd = "$relayCommand -open 1 -duration 500";
                exec($cmd, $output, $returnCode);

                if ($returnCode === 0) {
                    Log::info('Cash Drawer - USB relay activated', [
                        'command' => $cmd,
                        'output' => implode("\n", $output)
                    ]);
                    return true;
                }
            } else {
                // Linux USB relay approach (depends on specific hardware)
                $cmd = "usbrelay RELAY1=1 && sleep 0.5 && usbrelay RELAY1=0";
                exec($cmd, $output, $returnCode);
                
                if ($returnCode === 0) {
                    Log::info('Cash Drawer - Linux USB relay activated');
                    return true;
                }
            }

            throw new Exception("USB relay command failed with code: $returnCode");

        } catch (Exception $e) {
            Log::error('Cash Drawer - USB relay failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Method 4: Windows-specific command line approach
     */
    private function openDrawerWindowsCommand()
    {
        try {
            if (PHP_OS_FAMILY !== 'Windows') {
                throw new Exception("Windows command method only available on Windows");
            }

            // Using PowerShell to send signal
            $psCommand = "Add-Type -TypeDefinition 'using System; using System.IO.Ports; public class SerialHelper { public static void SendSignal(string port) { var serial = new SerialPort(port, 9600); serial.Open(); serial.Write(new byte[]{7}, 0, 1); serial.Close(); }}'; [SerialHelper]::SendSignal('$this->comPort')";
            
            $cmd = "powershell.exe -Command \"$psCommand\"";
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0) {
                Log::info('Cash Drawer - PowerShell command executed successfully');
                return true;
            }

            throw new Exception("PowerShell command failed with code: $returnCode");

        } catch (Exception $e) {
            Log::error('Cash Drawer - Windows command failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Test cash drawer connection and functionality
     */
    public function testDrawer()
    {
        try {
            Log::info('Cash Drawer - Testing drawer functionality');

            $testResult = $this->openDrawer(1);
            
            return [
                'success' => $testResult,
                'message' => $testResult ? 'Cash drawer test successful' : 'Cash drawer test failed',
                'configuration' => [
                    'type' => $this->drawerType,
                    'port' => $this->comPort,
                    'baud_rate' => $this->baudRate,
                    'os' => PHP_OS_FAMILY
                ],
                'timestamp' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error('Cash Drawer - Test failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Cash drawer test exception: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Get available COM ports (Windows)
     */
    public function getAvailablePorts()
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                exec('powershell.exe -Command "Get-WmiObject -Query \\"SELECT * FROM Win32_SerialPort\\" | Select-Object Name,DeviceID"', $output);
                return $output;
            } else {
                exec('ls /dev/tty*', $output);
                return array_filter($output, function($port) {
                    return strpos($port, 'USB') !== false || strpos($port, 'ACM') !== false;
                });
            }
        } catch (Exception $e) {
            Log::error('Cash Drawer - Could not get available ports', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get connection info and diagnostics
     */
    public function getConnectionInfo()
    {
        return [
            'drawer_type' => $this->drawerType,
            'com_port' => $this->comPort,
            'baud_rate' => $this->baudRate,
            'os_family' => PHP_OS_FAMILY,
            'available_ports' => $this->getAvailablePorts(),
            'timestamp' => now()->toISOString()
        ];
    }
}