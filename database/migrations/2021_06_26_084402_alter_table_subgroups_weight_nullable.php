<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubgroupsWeightNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_product_subgroups', function (Blueprint $table) {
            $table->unsignedInteger('weight')
                ->default(0)
                ->nullable(true)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_product_subgroups', function (Blueprint $table) {
            $table->unsignedInteger('weight')
                ->default(0)
                ->nullable(false)
                ->change();
        });
    }
}
