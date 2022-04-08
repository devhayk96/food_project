<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoshubCategoryProductOptionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_category_product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->foreign('menu_id')
                ->references('id')
                ->on('poshub_menus')
                ->onDelete('cascade');

            $table->unsignedBigInteger('menu_category_id');
            $table->foreign('menu_category_id')
                ->references('id')
                ->on('poshub_menu_categories')
                ->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('poshub_products')
                ->onDelete('cascade');

            $table->unsignedBigInteger('optional_group_id');
            $table->foreign('optional_group_id')
                ->references('id')
                ->on('poshub_optional_groups')
                ->onDelete('cascade');
            $table->unsignedInteger('weight')
                ->nullable(true)
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_category_product_option_groups');
    }
}
