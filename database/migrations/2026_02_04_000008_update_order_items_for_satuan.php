<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderItemsForSatuan extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key if exists (might vary by driver, doing safe approach)
            // sqlite doesn't support dropping foreign keys easily, so we just add the new column and make the old one nullable
            $table->foreignId('unit_type_id')->nullable()->constrained();
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('unit_type_id');
        });
    }
}
