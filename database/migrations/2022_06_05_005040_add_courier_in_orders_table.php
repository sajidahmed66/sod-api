<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('courier_id')->nullable()->after('status');
            $table->string('courier_branch')->nullable()->after('courier_id');
            $table->string('courier_tracking_no')->nullable()->after('courier_branch');
            $table->string('courier_status')->nullable()->after('courier_tracking_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('courier_id');
            $table->dropColumn('courier_branch');
            $table->dropColumn('courier_tracking_no');
            $table->dropColumn('courier_status');
        });
    }
}
