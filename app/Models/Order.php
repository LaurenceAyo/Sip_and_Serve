<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'subtotal',
        'tax_amount', 
        'maya_reference',
        'maya_webhook_received_at',
        'discount_amount',
        'total_amount',
        'cash_amount',
        'change_amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'cashier_id',
        'kitchen_received_at',
        'started_at',
        'completed_at',
        'paid_at',
        'estimated_prep_time', // Add this field - in minutes
        'paymongo_payment_intent_id',
        'paymongo_payment_method_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['processing_time', 'estimated_completion_time'];


    public function getFormattedCompletedAtAttribute()
{
    return $this->completed_at ? $this->completed_at->format('g:i A') : 'N/A';
}

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'kitchen_received_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'maya_webhook_received_at' => 'datetime',
        'estimated_prep_time' => 'integer', // in minutes
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // Calculate processing time in MM:SS format
    public function getProcessingTimeAttribute()
    {
        if (!$this->started_at) {
            return '00:00';
        }

        $startTime = Carbon::parse($this->started_at);
        $endTime = $this->completed_at ? Carbon::parse($this->completed_at) : now();
        
        $diffInSeconds = $startTime->diffInSeconds($endTime);
        $minutes = floor($diffInSeconds / 60);
        $seconds = $diffInSeconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // Calculate estimated completion time
    public function getEstimatedCompletionTimeAttribute()
    {
        if (!$this->started_at || !$this->estimated_prep_time) {
            return null;
        }

        return Carbon::parse($this->started_at)->addMinutes($this->estimated_prep_time);
    }

    // Check if order is overdue
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed' || !$this->estimated_completion_time) {
            return false;
        }

        return now()->gt($this->estimated_completion_time);
    }

    // Get total preparation time after completion
    public function getTotalPrepTimeAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return Carbon::parse($this->started_at)->diffInMinutes(Carbon::parse($this->completed_at));
    }

    // Auto-calculate estimated prep time based on order items
    public function calculateEstimatedPrepTime()
    {
        $totalPrepTime = 0;
        
        foreach ($this->orderItems as $item) {
            // Assume each menu item has a prep_time_minutes field
            $itemPrepTime = $item->menuItem->prep_time_minutes ?? 5; // default 5 minutes
            $totalPrepTime += ($itemPrepTime * $item->quantity);
        }
        
        // Add base time and consider parallel cooking
        $estimatedTime = max(15, ceil($totalPrepTime * 0.7)); // 70% efficiency for parallel cooking
        
        $this->update(['estimated_prep_time' => $estimatedTime]);
        
        return $estimatedTime;
    }

    public function generateOrderNumber()
    {
        $lastOrder = self::whereDate('created_at', today())->orderBy('id', 'desc')->first();
        $number = $lastOrder ? ($lastOrder->id + 1) : 1;
        return 'C' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'pending', // Keep as pending until kitchen starts
            'paid_at' => now(),
        ]);
    }

    public function isGCashPayment()
    {
        return $this->payment_method === 'gcash';
    }

    public function isCashPayment()
    {
        return $this->payment_method === 'cash';
    }
}