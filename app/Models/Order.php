<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status',
        'order_type',
        'total_amount',
        'paid_amount',
        'payment_status',
        'order_date',
        'completion_date',
        'service_duration_id',
        'service_type_id',
        'fragrance_id',
        'discount_id',
        'weight',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    protected $appends = ['total_amount_formatted', 'paid_amount_formatted'];

    public function getTotalAmountFormattedAttribute()
    {
        return $this->total_amount !== null
            ? 'Rp ' . number_format($this->total_amount, 0, ',', '.')
            : '-';
    }

    public function getPaidAmountFormattedAttribute()
    {
        return $this->paid_amount !== null
            ? 'Rp ' . number_format($this->paid_amount, 0, ',', '.')
            : '-';
    }

    public function getItemsSummaryAttribute()
    {
        $parts = [];

        // Kiloan part
        if ($this->weight > 0) {
            $parts[] = "Kiloan – " . (float)$this->weight . " kg";
        }

        // Satuan part
        if ($this->orderItems->isNotEmpty()) {
            foreach ($this->orderItems as $item) {
                $unit = $item->unitType;
                if ($unit) {
                    $mode = $unit->measure_mode;
                    if ($mode && strtolower($mode) !== 'pcs') {
                        $parts[] = "{$unit->name} {$item->quantity} {$mode}";
                    } else {
                        $parts[] = "{$unit->name} x{$item->quantity}";
                    }
                }
            }
        }

        return implode(', ', $parts);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function serviceDuration()
    {
        return $this->belongsTo(ServiceDuration::class);
    }

    public function fragrance()
    {
        return $this->belongsTo(Fragrance::class);
    }

    protected static function booted()
    {
        static::saving(function ($order) {
            if ($order->paid_amount >= $order->total_amount) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'unpaid';
            }
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
