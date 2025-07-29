<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentsColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('bkash_no')->nullable()->after('facebook_page_id');
            $table->string('bkash_type')->default('personal')->after('bkash_no');
            $table->string('nogod_no')->nullable()->after('bkash_type');
            $table->string('nogod_type')->default('personal')->after('nogod_no');
            $table->string('rocket_no')->nullable()->after('nogod_type');
            $table->string('rocket_type')->default('personal')->after('rocket_no');
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
            $table->dropColumn('bkash_no');
            $table->dropColumn('bkash_type');
            $table->dropColumn('nogod_no');
            $table->dropColumn('nogod_type');
            $table->dropColumn('rocket_no');
            $table->dropColumn('rocket_type');
        });
    }
}
