<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderAddKeysForApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('kitchen_id')
                ->nullable(true)
                ->after('table_id');
            $table->foreign('kitchen_id')
                ->references('id')
                ->on('poshub_kitchens')
                ->onDelete('cascade');

            $table->string('courier_id')
                ->nullable(true)
                ->after('kitchen_id');

            $table->boolean('is_discount_percentage')
                ->default(1)
                ->after('is_paid');
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
            $table->dropColumn('is_discount_percentage');
            $table->dropColumn('courier_id');
            $table->dropForeign('poshub_orders_kitchen_id_foreign');
            $table->dropColumn('kitchen_id');
        });
    }
}
