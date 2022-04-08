<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique()->nullable();
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                ->references('id')->on('poshub_shops')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('orders_auto_refresh_time')->nullable()->comment('milliseconds');
            $table->integer('finished_orders_delay_time')->nullable()->comment('milliseconds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_devices');
    }
}
