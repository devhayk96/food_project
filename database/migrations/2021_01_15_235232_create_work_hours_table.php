<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->nullable(false);
            $table->integer('day')
                ->nullable(false)
                ->comment('The day of the week express in number from 0 for Sunday to 6 for Saturday');
            $table->enum('type', ['opening', 'delivery'])
                ->nullable(false);
            $table->string('opening_hour')
                ->nullable(false);
            $table->string('closing_hour')
                ->nullable(false);
            $table->boolean('is_open')
                ->nullable(false)
                ->default(false);
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
        Schema::dropIfExists('poshub_work_hours');
    }
}
