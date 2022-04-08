<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateWorkDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->nullable(false)
                ->index();
            $table->date('date')
                ->nullable(false)
                ->index()
                ->comment('The date of the related work day');
            $table->tinyInteger('shift')
                ->nullable(false);
            $table->integer('orders', false, true)
                ->nullable(false)
                ->comment('The total number of the orders executed for that work day');
            $table->boolean('is_open')
                ->nullable(false)
                ->index()
                ->comment('The status of the related shop for that work day at the moment of the query');
            $table->dateTime('opening_datetime')
                ->nullable(false);
            $table->dateTime('closing_datetime')
                ->nullable()
                ->comment('Cannot be more than 7am of the day after the opening_datetime of the in locale timezone');
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
        Schema::dropIfExists('poshub_work_days');
    }
}
