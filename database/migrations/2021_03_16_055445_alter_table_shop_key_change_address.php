<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableShopKeyChangeAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_shops', function (Blueprint $table) {
            $table->dropColumn('address_id');

            $table->dropColumn('ubereats_restaurant_id');

            $table->json('address')
                ->after('email')
                ->nullable(true);

            $table->unsignedBigInteger('currency_id')
                ->after('id')
                ->nullable(true);
            $table->foreign('currency_id')
                ->references('id')
                ->on('poshub_currencies')
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
        Schema::table('poshub_shops', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
            $table->dropColumn('address');
            $table->foreignId('address_id')
                ->nullable(false);
            $table->string('ubereats_restaurant_id', 256)
                ->nullable()
                ->unique()
                ->comment('If set, represent the id of the restaurant in the uber eats system.');
        });
    }
}
