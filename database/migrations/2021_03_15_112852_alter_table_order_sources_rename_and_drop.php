<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderSourcesRenameAndDrop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::drop('poshub_order_source_shop');
//        Schema::dropIfExists('poshub_order_source_shop');

        Schema::table('poshub_order_sources', function (Blueprint $table){
//            $table->dropIndex('order_source_type_id');
        });
        Schema::rename('poshub_order_sources', 'poshub_order_source_shops');

        Schema::table('poshub_order_source_shops', function (Blueprint $table) {

            $table->foreign('order_source_type_id')
                ->references('id')
                ->on('poshub_order_source_types')
                ->onDelete('cascade');

            $table->unsignedBigInteger('shop_id')
                ->after('order_source_type_id')
                ->default(1);

            $table->foreign('shop_id')
                ->references('id')
                ->on('poshub_shops')
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
        Schema::table('poshub_order_source_shops', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
            $table->dropForeign(['order_source_type_id']);
        });

        Schema::rename('poshub_order_source_shops', 'poshub_order_sources');
    }
}
