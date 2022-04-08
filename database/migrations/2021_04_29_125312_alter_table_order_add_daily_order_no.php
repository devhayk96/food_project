<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderAddDailyOrderNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('daily_order_number')
                ->nullable(true)
                ->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->dropColumn('daily_order_number');
        });
    }
}
