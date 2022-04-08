<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateMenuOptionalProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_menu_optional_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_product_id');
            $table->foreign('menu_product_id')
                ->references('id')
                ->on('poshub_menu_products')
                ->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('poshub_products')
                ->onDelete('cascade');

            $table->unsignedDecimal('price')
                ->default(0);

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
        Schema::dropIfExists('poshub_menu_optional_products');
    }
}
