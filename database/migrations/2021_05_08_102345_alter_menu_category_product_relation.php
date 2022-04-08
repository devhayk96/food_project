<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMenuCategoryProductRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_menu_category_products', function (Blueprint $table) {
            $table->dropForeign(['menu_category_id']);
            $table->foreign('menu_category_id')
                ->references('id')
                ->on('poshub_menu_category_links')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_menu_category_products', function (Blueprint $table) {
            $table->dropForeign(['menu_category_id']);
            $table->foreign('menu_category_id')
                ->references('id')
                ->on('poshub_menu_categories')
                ->onDelete('cascade');
        });
    }
}
