<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderProductAddDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_order_products', function (Blueprint $table) {
            $table->boolean('is_discount_percentage')->after('vat_amount')->default(1);
            $table->unsignedDecimal('discount')->after('is_discount_percentage')->default(0);
            $table->unsignedDecimal('discounted_amount')->after('discount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_order_products', function (Blueprint $table) {
            $table->dropColumn('discounted_amount');
            $table->dropColumn('discount');
            $table->dropColumn('is_discount_percentage');
        });
    }
}
