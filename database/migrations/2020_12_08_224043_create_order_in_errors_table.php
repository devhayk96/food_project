<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOrderInErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_orders_in_error', function (Blueprint $table) {
            $table->id();
            $table->string('message', 512)
                ->nullable(false)
                ->index();
            $table->text('errors')
                ->nullable(false);
            $table->text('order_in_transit')
                ->nullable(false);
            $table->boolean('force')
                ->default(false)
                ->nullable(false);
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
        Schema::dropIfExists('poshub_orders_in_error');
    }
}
