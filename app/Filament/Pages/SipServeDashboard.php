<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class SipServeDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.sip-serve-dashboard';
    protected static ?string $title = 'SIP Serve Dashboard';
    protected static ?string $navigationLabel = 'SIP Dashboard';
    protected static ?int $navigationSort = 1;
    
    // Set custom URL for direct access
    protected static string $routePath = '/sip-serve-dashboard';
    
    // Remove default layout elements
    protected static bool $shouldRegisterNavigation = true;
    
    // ... rest of your existing code remains the same
    public $activeTab = 'inventory';
    public $selectedFilter = 'ALL ITEMS';
    public $isConnected = false;
    public $connectionStatus = 'Disconnected';
    public $activeCalls = 0;
    public $serverUptime = '00:00:00';
    
    public $inventoryItems = [
        ['name' => 'Almond Milk', 'inStock' => 20, 'out' => 3.0, 'current' => '17.0 liters', 'status' => 'good'],
        ['name' => 'Arabica Coffee Beans', 'inStock' => 20, 'out' => 5.5, 'current' => '14.5 kg', 'status' => 'good'],
        ['name' => 'Caramel Syrup', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 liters', 'status' => 'low'],
        ['name' => 'Espresso Blend', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 kg', 'status' => 'good'],
        ['name' => 'Kape Barako Beans', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 kg', 'status' => 'critical'],
        ['name' => 'Whole Milk', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 liters', 'status' => 'critical'],
        ['name' => 'White Sugar', 'inStock' => 20, 'out' => 15.6, 'current' => '4.4 kg', 'status' => 'good'],
        ['name' => 'Milk', 'inStock' => 20, 'out' => 3.2, 'current' => '8.2 kg', 'status' => 'good'],
        ['name' => 'Eggs', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 kg', 'status' => 'critical'],
        ['name' => 'Bread', 'inStock' => 20, 'out' => 3.2, 'current' => '16.8 liters', 'status' => 'critical'],
        ['name' => 'Kape Barako Beans', 'inStock' => 20, 'out' => 15.6, 'current' => '4.4 kg', 'status' => 'good'],
    ];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Initialize SIP connection status
        $this->checkSipConnection();
        
        // Show welcome notification
        Notification::make()
            ->title('Welcome to SIP Serve Dashboard')
            ->body('Successfully connected to L\' Primero Cafe management system.')
            ->success()
            ->send();
    }
    
    // ... rest of your existing methods remain the same
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function setSelectedFilter($filter)
    {
        $this->selectedFilter = $filter;
    }
    
    public function connectToSip()
    {
        $this->isConnected = true;
        $this->connectionStatus = 'Connected';
        
        Notification::make()
            ->title('SIP Server Connected')
            ->body('Successfully connected to SIP server.')
            ->success()
            ->send();
    }
    
    public function disconnectFromSip()
    {
        $this->isConnected = false;
        $this->connectionStatus = 'Disconnected';
        $this->activeCalls = 0;
        
        Notification::make()
            ->title('SIP Server Disconnected')
            ->body('Disconnected from SIP server.')
            ->warning()
            ->send();
    }
    
    public function refreshConnection()
    {
        $this->checkSipConnection();
        
        Notification::make()
            ->title('Connection Status Refreshed')
            ->body("Current status: {$this->connectionStatus}")
            ->info()
            ->send();
    }
    
    private function checkSipConnection()
    {
        $this->isConnected = rand(0, 1) === 1;
        $this->connectionStatus = $this->isConnected ? 'Connected' : 'Disconnected';
        $this->activeCalls = $this->isConnected ? rand(0, 5) : 0;
        $this->serverUptime = $this->isConnected ? '02:45:30' : '00:00:00';
    }
    
    public function getStatusColor($status)
    {
        switch($status) {
            case 'good': return 'bg-green-500';
            case 'low': return 'bg-yellow-400';
            case 'critical': return 'bg-red-500';
            default: return 'bg-gray-300';
        }
    }
    
    public function getConnectionStatusColor()
    {
        return $this->isConnected ? 'text-green-600' : 'text-red-600';
    }
    
    public function getConnectionStatusIcon()
    {
        return $this->isConnected ? 'heroicon-s-check-circle' : 'heroicon-s-x-circle';
    }
}