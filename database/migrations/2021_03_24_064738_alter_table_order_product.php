<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('vat_id')
                ->after('product_id')
                ->nullable(true);
            $table->foreign('vat_id')
                ->references('id')
                ->on('poshub_vats')
                ->onDelete('cascade');

            $table->unsignedDouble('vat_amount')
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
        Schema::table('poshub_order_products', function (Blueprint $table) {
            $table->dropColumn('vat_amount');
            $table->dropForeign(['vat_id']);
            $table->dropColumn('vat_id');
        });
    }
}
