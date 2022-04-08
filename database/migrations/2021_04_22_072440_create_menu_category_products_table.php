<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateMenuCategoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_menu_category_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_product_id');
            $table->foreign('menu_product_id')
                ->references('id')
                ->on('poshub_menu_products')
                ->onDelete('cascade');

            $table->unsignedBigInteger('menu_category_id');
            $table->foreign('menu_category_id')
                ->references('id')
                ->on('poshub_menu_categories')
                ->onDelete('cascade');

            $table->boolean('is_blocked')
                ->default(0);

            $table->string('weight')->nullable(true);

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
        Schema::dropIfExists('poshub_menu_category_products');
    }
}
