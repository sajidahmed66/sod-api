<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_no');
            $table->string('name');
            $table->string('mobile');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('area_id');
            $table->longText('address');
            $table->string('note')->nullable();
            $table->string('payment');
            $table->float('sub_total');
            $table->float('shipping_cost')->default(0);
            $table->float('total')->default(0);
            $table->float('paid')->default(0);
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
