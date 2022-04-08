<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_type_id')
                ->nullable(false);
            $table->timestamp('executed_time', 0)
                ->nullable()
                ->comment('The executed delivery time.');
            $table->decimal('delivery_costs')
                ->nullable();
            $table->string('delivery_remark', 4096)
                ->nullable();
            $table->foreignUuid('order_id')
                ->nullable();
            $table->foreignId('address_id')
                ->nullable()
                ->comment('The address id related to this delivery.');
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
        Schema::dropIfExists('poshub_deliveries');
    }
}
