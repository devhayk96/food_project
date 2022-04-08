<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOrderSourceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_source_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)
                ->nullable(false)
                ->index();
            $table->string('name', 256)
                ->nullable(false);
            $table->string('client_class', 512)
                ->nullable();
            $table->string('credentials_class', 512)
                ->nullable();
            $table->boolean('is_active')
                ->default(false)
                ->index();
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
        Schema::dropIfExists('poshub_order_source_types');
    }
}
