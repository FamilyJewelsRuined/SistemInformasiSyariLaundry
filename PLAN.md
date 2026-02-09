# Laundry Management System - Detailed Implementation Plan

## 1. System Overview
This is a web-based information system tailored for a laundry business owner. The system is designed to single-handedly manage operations, track orders, manage customers, and generate financial reports.

**Technology Stack:**
- **Framework:** Laravel 8 (Compatible with PHP 7.4)
- **Admin Panel:** Filament v1.x (Legacy version compatible with PHP 7.4) OR Custom TALL Stack Dashboard
- **Frontend/Interactivity:** Livewire & Alpine.js
- **Styling:** Tailwind CSS
- **Database:** PostgreSQL (Supabase)

> **Constraint Note:** The system is running **PHP 7.4**. Modern Filament (v3) requires PHP 8.1+. We will utilize **Filament v1.x** or a custom **Livewire+Tailwind** dashboard to achieve the requested functionality within the existing environment constraints.

---

## 2. Database Schema (Supabase / PostgreSQL)

The database is designed to handle customers, services, orders, and financial tracking. We will use the `pgsql` driver.

### Core Tables

1.  **`users`**
    - `id`, `name`, `email`, `password`
    - *Role:* Owner

2.  **`customers`**
    - `id`, `name`, `phone`, `address`, `email` (optional), `notes`
    - *HasMany* `orders`

3.  **`services`**
    - `id`, `name` (e.g., "Wash & Fold")
    - `price` (Decimal)
    - `unit` (e.g., "kg", "pcs")
    - `is_active` (Boolean)

4.  **`orders`**
    - `id`, `customer_id` (FK)
    - `status` (String/Enum: Pending, Processing, Ready, Completed, Cancelled)
    - `total_amount` (Decimal)
    - `paid_amount` (Decimal)
    - `payment_status` (String)
    - `order_date` (Timestamp)
    - `completion_date` (Timestamp, nullable)

5.  **`order_items`**
    - `id`, `order_id` (FK)
    - `service_id` (FK)
    - `quantity` (Integer/Decimal)
    - `unit_price` (Decimal)
    - `subtotal` (Decimal)

6.  **`expenses`**
    - `id`, `description`, `amount`, `date`, `category`

---

## 3. Filament Resources & UI Design

### Dashboard
**Goal:** Instant overview of daily operations.
- **Widgets:**
    - Stats: Revenue Today, Active Orders.
    - Recent Orders Table.

### Customer Management
- **List:** Searchable table (Name, Phone).
- **Create:** Simple form.
- **Relation:** View Customer -> See Orders history.

### Service Management
- **List:** Table of services.
- **Create:** Name, Price, Unit.

### Order Management (Core)
- **List:** Filter by Status (Pending, Ready).
- **Create/Edit:**
    - **Customer:** Select/Search.
    - **Items (Repeater):** Add multiple services (Service + Qty).
        - *Note:* In Filament v1, repeaters are available but might require specific configuration.
    - **Calculations:** Auto-update subtotal/total.

### Reports
- Custom page with Date Range filter showing Total Sales and Net Profit.

---

## 4. Implementation Details

### A. Environment Configuration (.env)
```ini
DB_CONNECTION=pgsql
DB_HOST=your-supabase-db.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password
```

### B. Migrations
*(Standard Laravel Migrations applied to Postgres)*

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->string('status')->default('pending');
    $table->decimal('total_amount', 10, 2);
    $table->decimal('paid_amount', 10, 2)->default(0);
    $table->string('payment_status')->default('unpaid');
    $table->timestamp('order_date')->useCurrent();
    $table->timestamp('completion_date')->nullable();
    $table->timestamps();
});
```

### C. Filament Logic (v1.x style) or Livewire Component
Since we are on PHP 7.4, syntax like `Enum` is not available. We will use string literals or constants.

```php
// OrderResource.php (Conceptual v1 adaptation)
public static function form(Form $form)
{
    return $form->schema([
        Select::make('customer_id')
            ->relationship('customer', 'name')
            ->required(),
        Repeater::make('items')
             ->schema([
                 Select::make('service_id')->relationship('service', 'name'),
                 TextInput::make('quantity')->numeric(),
             ]),
        Select::make('status')
            ->options([
                'pending' => 'Pending',
                'ready' => 'Ready',
                'completed' => 'Completed',
            ])
    ]);
}
```

## 5. Next Steps for Execution

1.  **Configure Database:** Update `.env` with your Supabase credentials.
2.  **Install Filament:**
    ```bash
    composer require filament/filament:"^1.0"
    php artisan migrate
    php artisan filament:install
    ```
3.  **Generate Resources:**
    ```bash
    php artisan make:filament-resource Customer
    php artisan make:filament-resource Service
    php artisan make:filament-resource Order
    ```
4.  **Develop Logic:** Implement the dynamic calculations in the Order form.
