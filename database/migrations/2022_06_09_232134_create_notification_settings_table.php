<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id');
            $table->boolean('customer_new_order_email')->default(0);
            $table->boolean('customer_order_status_change_email')->default(0);
            $table->boolean('customer_payment_approved_email')->default(0);
            $table->boolean('customer_new_order_sms')->default(0);
            $table->boolean('customer_order_status_change_sms')->default(0);
            $table->boolean('customer_payment_approved_sms')->default(0);
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
        Schema::dropIfExists('notification_settings');
    }
}
