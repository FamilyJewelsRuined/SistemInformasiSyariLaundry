<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fragrance extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    protected $appends = ['price_formatted'];

    public function getPriceFormattedAttribute()
    {
        return $this->price !== null
            ? 'Rp ' . number_format($this->price, 0, ',', '.')
            : '-';
    }
}
