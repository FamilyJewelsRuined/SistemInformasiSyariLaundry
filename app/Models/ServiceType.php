<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price_per_kg'];

    protected $appends = ['price_per_kg_formatted'];

    public function getPricePerKgFormattedAttribute()
    {
        return $this->price_per_kg !== null
            ? 'Rp ' . number_format($this->price_per_kg, 0, ',', '.')
            : '-';
    }
}
