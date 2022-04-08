<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDiscountAddKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_discounts', function (Blueprint $table) {
            $table->boolean('is_percentage')->default(1)->after('name');
            $table->boolean('is_employee_discount')->default(0)->after('value');
            $table->boolean('is_own_use')->default(0)->after('is_employee_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_discounts', function (Blueprint $table) {
            $table->dropColumn('is_own_use');
            $table->dropColumn('is_employee_discount');
            $table->dropColumn('is_percentage');
        });
    }
}
