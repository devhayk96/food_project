<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_menus', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->text('description')
                ->nullable(true);

            $table->dateTime('start_date')
                ->nullable(true);

            $table->dateTime('end_date')
                ->nullable(true);

            $table->boolean('is_blocked')
                ->default(0);

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
        Schema::dropIfExists('poshub_menus');
    }
}
