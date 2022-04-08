<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->unsignedDouble('delivery_vat_amount')
                ->after('delivery_cost')
                ->default(0);
            $table->unsignedDouble('total_vat_amount')
                ->after('total_price')
                ->default(0);
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
            $table->dropColumn('total_vat_amount');
            $table->dropColumn('delivery_vat_amount');
        });
    }
}
