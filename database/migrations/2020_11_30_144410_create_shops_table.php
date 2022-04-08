<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_shops', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)
                ->nullable(false);
            $table->string('phone', 64)
                ->nullable()
                ->index();
            $table->string('email', 256)
                ->nullable()
                ->index();
            $table->string('company_number', 256)
                ->nullable()
                ->index()
                ->comment('Chamber of Commerce number');
            $table->string('vat', 256)
                ->nullable()
                ->index();
            $table->string('iban', 256)
                ->nullable()
                ->index();
            $table->integer('delivery_time')
                ->nullable(false)
                ->default(45)
                ->index();
            $table->integer('pickup_time')
                ->nullable(false)
                ->default(25)
                ->index();
            $table->foreignId('address_id')
                ->nullable(false);
            $table->string('ubereats_restaurant_id', 256)
                ->nullable()
                ->unique()
                ->comment('If set, represent the id of the restaurant in the uber eats system.');
            $table->boolean('is_open')
                ->nullable(false)
                ->index()
                ->comment('When true, the shop is opened.');
            $table->boolean('is_delivering')
                ->nullable(false)
                ->index()
                ->comment('When true, the shop is opened.');
            $table->boolean('is_active')
                ->nullable(false)
                ->index();
            $table->boolean('is_visible')
                ->nullable(false)
                ->index()
                ->comment('When true, the shop is active but invisible from the web shops.');
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
        Schema::dropIfExists('poshub_shops');
    }
}
