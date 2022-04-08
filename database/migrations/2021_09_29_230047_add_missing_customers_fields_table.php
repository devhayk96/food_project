<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingCustomersFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_customers', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->string('order_confirmation')->default(0)->nullable();
            $table->string('mailinglist')->default(0)->nullable();
            $table->string('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_customers', function (Blueprint $table) {
            //
        });
    }
}
