<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDiscountForOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_order_discount', function (Blueprint $table) {
            $table->json('discount_json')->nullable(true);
            $table->foreign('order_id')
                ->references('id')
                ->on('poshub_orders')
                ->onDelete('cascade');
            $table->foreign('discount_id')
                ->references('id')
                ->on('poshub_discounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_order_discount', function (Blueprint $table) {
            $table->dropColumn('discount_json');
        });
    }
}
