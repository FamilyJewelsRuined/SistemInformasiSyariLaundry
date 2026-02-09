<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixOrderItemsNullable extends Migration
{
    public function up()
    {
        Schema::create('order_items_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained(); // Made nullable
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreignId('unit_type_id')->nullable()->constrained(); // From previous update
            $table->timestamps();
        });

        // Copy data
        $items = DB::table('order_items')->get();
        foreach ($items as $item) {
            DB::table('order_items_temp')->insert((array) $item);
        }

        Schema::drop('order_items');
        Schema::rename('order_items_temp', 'order_items');
    }

    public function down()
    {
        // simplistic down
    }
}
