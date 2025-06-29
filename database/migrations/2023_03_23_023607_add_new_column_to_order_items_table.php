<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_price_id')->nullable()->after('product_name')->references('id')->on('product_prices')->onDelete('cascade')->onUpdate('cascade');
            $table->string('product_price_name')->nullable()->after('product_price_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_price_id');
            $table->dropColumn('product_price_name');
        });
    }
}
