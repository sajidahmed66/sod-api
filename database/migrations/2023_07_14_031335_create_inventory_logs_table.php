<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('type');
            $table->double('qty', 8, 2)->default(0);
            $table->double('unit_price', 8, 2)->default(0);
            $table->double('amount', 8, 2)->default(0);
            $table->double('paid', 8, 2)->default(0);
            $table->string('vendor')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
}
