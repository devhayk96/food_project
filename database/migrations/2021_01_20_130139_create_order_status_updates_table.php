<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOrderStatusUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')
                ->nullable(false);
            $table->foreignId('order_status_id')
                ->nullable(false);
            $table->text('additional_data')
                ->nullable(true);
            $table->defineAsEntity();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_order_status_updates');
    }
}
