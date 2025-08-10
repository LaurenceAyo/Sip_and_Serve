<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'subtotal',
        'tax_amount', 
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
        'paymongo_payment_intent_id',
        'paymongo_payment_method_id',
        'created_at',
        'updated_at'
    ];
    
    protected $appends = ['id']; 
    
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
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // Add these new methods for PayMongo integration
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
            'status' => 'preparing',
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