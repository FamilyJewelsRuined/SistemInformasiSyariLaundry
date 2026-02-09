<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'service_id', 'unit_type_id', 'quantity', 'unit_price', 'subtotal'];

    protected $appends = ['unit_price_formatted', 'subtotal_formatted'];

    public function getUnitPriceFormattedAttribute()
    {
        return $this->unit_price !== null
            ? 'Rp ' . number_format($this->unit_price, 0, ',', '.')
            : '-';
    }

    public function getSubtotalFormattedAttribute()
    {
        return $this->subtotal !== null
            ? 'Rp ' . number_format($this->subtotal, 0, ',', '.')
            : '-';
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }
}
