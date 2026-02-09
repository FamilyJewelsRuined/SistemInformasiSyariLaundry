<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewMasterTables extends Migration
{
    public function up()
    {
        // 1. Waktu Layanan (Service Durations)
        Schema::create('service_durations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Regular, Express
            $table->string('description')->nullable(); // max 72 hrs
            $table->decimal('surcharge', 10, 2)->default(0); // Extra cost if any
            $table->timestamps();
        });

        // 2. Tipe Layanan (Service Types - mostly for Kiloan)
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cuci, Setrika, Cuci & Setrika
            $table->decimal('price_per_kg', 10, 2);
            $table->timestamps();
        });

        // 3. Tipe Satuan (Unit Types)
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Shoe, Carpet, Helmet
            $table->string('measure_mode')->default('quantity'); // quantity, meter
            $table->decimal('price', 10, 2); // Price per unit or per meter
            $table->timestamps();
        });

        // 4. Discounts
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // New Customer
            $table->string('type')->default('fixed'); // percent, fixed
            $table->decimal('value', 10, 2);
            $table->timestamps();
        });

        // 5. Fragrances
        Schema::create('fragrances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0); // Optional cost
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_durations');
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('unit_types');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('fragrances');
    }
}
