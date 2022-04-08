<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsChangeCourseNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->unsignedDecimal('course')
                ->nullable(true)
                ->default(null)
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
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->unsignedDecimal('course')
                ->nullable(false)
                ->default(0)
                ->change();
        });
    }
}
