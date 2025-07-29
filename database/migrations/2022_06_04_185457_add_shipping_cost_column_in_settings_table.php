<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingCostColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->float('shipping_cost_inside_dhaka')->default(0)->after('facebook_page_id');
            $table->float('shipping_cost_outside_dhaka')->default(0)->after('shipping_cost_inside_dhaka');
            $table->string('shipping_note')->nullable()->after('shipping_cost_outside_dhaka');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('shipping_cost_inside_dhaka');
            $table->dropColumn('shipping_cost_outside_dhaka');
            $table->dropColumn('shipping_note');
        });
    }
}
