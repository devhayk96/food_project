<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStagePiggyToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->boolean('piggy_stage')->default(0)->nullable();
            $table->boolean('piggy_stage_status')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            //
        });
    }
}
