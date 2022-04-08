<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddressForOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')
                ->after('id')
                ->nullable(true);
            $table->foreign('customer_id')
                ->references('id')
                ->on('poshub_customers')
                ->onDelete('cascade');

            $table->string('order_type')
                ->after('customer_id')
                ->default('billing');

            $table->string('type')
                ->after('order_type')
                ->default('home');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_addresses', function (Blueprint $table) {
            $table->dropColumn('order_type');
            $table->dropColumn('type');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
