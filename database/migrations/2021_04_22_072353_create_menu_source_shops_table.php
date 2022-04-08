<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateMenuSourceShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_menu_source_shops', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_id');
            $table->foreign('menu_id')
                ->references('id')
                ->on('poshub_menus')
                ->onDelete('cascade');

            $table->unsignedBigInteger('source_id');
            $table->foreign('source_id')
                ->references('id')
                ->on('poshub_order_source_types')
                ->onDelete('cascade');

            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                ->references('id')
                ->on('poshub_shops')
                ->onDelete('cascade');

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
        Schema::dropIfExists('poshub_menu_source_shops');
    }
}
