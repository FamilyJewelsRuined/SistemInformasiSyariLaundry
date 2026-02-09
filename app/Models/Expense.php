<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_date',
        'item_name',
        'quantity',
        'price',
        'total_amount',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($expense) {
            $expense->total_amount = $expense->quantity * $expense->price;
        });
    }
}
