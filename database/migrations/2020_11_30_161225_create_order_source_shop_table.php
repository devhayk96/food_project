<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSourceShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_source_shop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_source_id')
                ->nullable(false)
                ->index();
            $table->foreignId('shop_id')
                ->nullable(false)
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_order_source_shop');
    }
}
