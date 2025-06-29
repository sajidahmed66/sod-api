<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSteadfastColumnsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('steadfast_api_key')->nullable()->after('facebook_page_id');
            $table->string('steadfast_api_secret')->nullable()->after('steadfast_api_key');
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
            $table->dropColumn('steadfast_api_key');
            $table->dropColumn('steadfast_api_secret');
        });
    }
}
