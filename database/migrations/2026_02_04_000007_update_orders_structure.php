<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersStructure extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('kiloan'); // kiloan, satuan
            $table->foreignId('fragrance_id')->nullable()->constrained();
            $table->foreignId('service_duration_id')->nullable()->constrained();
            $table->foreignId('service_type_id')->nullable()->constrained(); // For Kiloan
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->decimal('weight', 8, 2)->nullable(); // For Kiloan
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'fragrance_id', 'service_duration_id', 'service_type_id', 'discount_id', 'weight']);
        });
    }
}
