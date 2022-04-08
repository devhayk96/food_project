<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDragOrderFieldToOptionalGroupProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_optional_group_products', function (Blueprint $table) {
            $table->unsignedBigInteger('drag_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_optional_group_products', function (Blueprint $table) {
            $table->dropColumn('drag_order');
        });
    }
}
