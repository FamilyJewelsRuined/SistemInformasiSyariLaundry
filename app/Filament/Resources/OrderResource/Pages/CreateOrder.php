<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Customer;
use App\Models\Fragrance;
use App\Models\ServiceDuration;
use App\Models\ServiceType;
use App\Models\UnitType;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    public static $resource = OrderResource::class;

    public static $view = 'filament.resources.orders.create-wizard';

    public $step = 1;
    public $data = [
        'customer_id' => '',
        'order_type' => 'kiloan', // kiloan, satuan
        'fragrance_id' => null,
        'service_duration_id' => null,
        'service_type_id' => null,
        'weight' => 0,
        'discount_id' => null,
        'payment_status' => 'unpaid',
        'paid_amount' => 0,
        'notes' => '',
    ];

    // Satuan items
    public $newItem = [
        'unit_type_id' => '',
        'quantity' => 1,
    ];
    public $orderItems = [];

    // Cached lookups
    public $customers;
    public $fragrances;
    public $durations;
    public $serviceTypes;
    public $unitTypes;
    public $discounts;

    public function mount()
    {
        // Don't call parent mount if we are fully overriding
        $this->customers = Customer::pluck('name', 'id');
        $this->fragrances = Fragrance::pluck('name', 'id');
        $this->durations = ServiceDuration::pluck('name', 'id');
        $this->serviceTypes = ServiceType::pluck('name', 'id');
        $this->unitTypes = UnitType::pluck('name', 'id');
        $this->discounts = Discount::pluck('name', 'id');
    }

    public function getSubtotalProperty()
    {
        $subtotal = 0;

        // Kiloan Logic
        if ($this->data['order_type'] === 'kiloan') {
            $type = ServiceType::find($this->data['service_type_id']);
            $weight = (float) ($this->data['weight'] ?? 0);
            if ($type) {
                $subtotal += $type->price_per_kg * $weight;
            }
        }

        // Satuan Items Logic (Always Add)
        foreach ($this->orderItems as $item) {
            $subtotal += $item['subtotal'];
        }

        // Fragrance Cost
        $fragrance = Fragrance::find($this->data['fragrance_id']);
        if ($fragrance) {
            $fragrancePrice = (float) $fragrance->price;
            $subtotal += $fragrancePrice; 
        }

        return $subtotal;
    }

    public function getSurchargeAmountProperty()
    {
        // Service Duration Surcharge
        if ($this->data['order_type'] === 'kiloan' && !empty($this->data['service_duration_id'])) {
            $duration = ServiceDuration::find($this->data['service_duration_id']);
            return $duration ? (float) $duration->surcharge : 0;
        }
        return 0;
    }

    public function getDiscountAmountProperty()
    {
        if (empty($this->data['discount_id'])) return 0;
        
        $discount = Discount::find($this->data['discount_id']);
        if (!$discount) return 0;

        $base = $this->subtotal + $this->surchargeAmount;

        if ($discount->type === 'percent') {
            return $base * ($discount->value / 100);
        } else {
            return (float) $discount->value;
        }
    }

    public function getGrandTotalProperty()
    {
        return max(0, ($this->subtotal + $this->surchargeAmount) - $this->discountAmount);
    }
    
    public function getPricePerKgProperty()
    {
         if (empty($this->data['service_type_id'])) return 0;
         $type = ServiceType::find($this->data['service_type_id']);
         return $type ? $type->price_per_kg : 0;
    }

    public function addItem()
    {
        $this->validate([
            'newItem.unit_type_id' => 'required',
            'newItem.quantity' => 'required|numeric|min:0.1',
        ]);

        $unit = UnitType::find($this->newItem['unit_type_id']);
        $qty = (float) $this->newItem['quantity'];
        $price = (float) $unit->price; // per unit or per meter
        $subtotal = $qty * $price;

        $this->orderItems[] = [
            'unit_type_id' => $unit->id,
            'name' => $unit->name,
            'quantity' => $qty,
            'unit_price' => $price,
            'subtotal' => $subtotal,
        ];

        // Reset inputs
        $this->newItem = ['unit_type_id' => '', 'quantity' => 1];
    }

    public function removeItem($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function nextStep()
    {
        // Validation per step
        if ($this->step === 1) {
            $this->validate(['data.customer_id' => 'required']);
        }
        
        if ($this->step === 2) {
            $this->validate([
                'data.order_type' => 'required',
                'data.fragrance_id' => 'nullable',
            ]);
            
            if ($this->data['order_type'] === 'kiloan') {
                $this->validate([
                     'data.service_duration_id' => 'required',
                     'data.service_type_id' => 'required',
                     'data.weight' => 'required|numeric|min:0.1',
                ]);
            }
            // Satuan moves to items in step 3
        }

        if ($this->step === 3) {
            if ($this->data['order_type'] === 'satuan' && count($this->orderItems) === 0) {
                 $this->addError('items', 'Please add at least one item.');
                 return;
            }
        }

        if ($this->step === 4) {
             $this->validate([
                 'data.payment_status' => 'required',
                 'data.paid_amount'    => 'numeric',
             ]);
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function create($another = false) // Override create method
    {
        DB::transaction(function () {
             $order = Order::create([
                 'customer_id' => $this->data['customer_id'],
                 'order_type' => $this->data['order_type'],
                 'status' => $this->data['status'] ?? 'pending',
                 'payment_status' => $this->data['payment_status'],
                 'total_amount' => $this->grandTotal,
                 'paid_amount' => ($this->data['payment_status'] === 'paid') 
                        ? ($this->data['paid_amount'] ?: $this->grandTotal) 
                        : 0,
                 'order_date' => now(),
                 // Kiloan specific
                 'service_duration_id' => $this->data['order_type'] === 'kiloan' ? $this->data['service_duration_id'] : null,
                 'service_type_id' => $this->data['order_type'] === 'kiloan' ? $this->data['service_type_id'] : null,
                 'fragrance_id' => $this->data['fragrance_id'] ?? null,
                 'weight' => $this->data['order_type'] === 'kiloan' ? $this->data['weight'] : null,
                 'discount_id' => $this->data['discount_id'] ?? null,
                 'notes' => $this->data['notes'] ?? null,
             ]);

             // Save Items (Always iterate if items exist)
             foreach ($this->orderItems as $item) {
                 OrderItem::create([
                     'order_id' => $order->id,
                     'unit_type_id' => $item['unit_type_id'],
                     'quantity' => $item['quantity'],
                     'unit_price' => $item['unit_price'],
                     'subtotal' => $item['subtotal'],
                 ]);
             }
        });

        $this->redirect(OrderResource::generateUrl('index'));
    }
}
