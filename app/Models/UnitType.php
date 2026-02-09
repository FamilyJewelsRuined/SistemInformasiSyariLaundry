<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'measure_mode', 'price'];

    protected $appends = ['price_formatted'];

    public function getPriceFormattedAttribute()
    {
        return $this->price !== null
            ? 'Rp ' . number_format($this->price, 0, ',', '.')
            : '-';
    }
}
