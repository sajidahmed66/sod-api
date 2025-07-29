<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPathaoConfigarationToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('pathao_client_id')->nullable()->after('steadfast_api_key');
            $table->string('pathao_client_secret')->nullable()->after('pathao_client_id');
            $table->integer('pathao_store_id')->nullable()->after('pathao_client_secret');
            $table->string('pathao_password')->nullable()->after('pathao_store_id');
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
            $table->dropColumn('pathao_client_id');
            $table->dropColumn('pathao_client_secret');
            $table->dropColumn('pathao_store_id');
            $table->dropColumn('pathao_password');
        });
    }
}
