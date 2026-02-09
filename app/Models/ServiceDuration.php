<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDuration extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'surcharge'];

    protected $appends = ['surcharge_formatted'];

    public function getSurchargeFormattedAttribute()
    {
        return $this->surcharge !== null
            ? 'Rp ' . number_format($this->surcharge, 0, ',', '.')
            : '-';
    }
}
