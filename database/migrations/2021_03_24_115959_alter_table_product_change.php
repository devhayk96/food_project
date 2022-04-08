<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->renameColumn('subcategory_id','product_subgroup_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->renameColumn('product_subgroup_id', 'subcategory_id');
        });
    }
}
