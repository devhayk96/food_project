<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBooleanKeysToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->string('ean')
                ->nullable(true);
            $table->boolean('is_receipt')
                ->default(0);
            $table->boolean('is_kitchen')
                ->default(0);
            $table->boolean('is_sticker')
                ->default(0);
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
            $table->dropColumn('ean');
            $table->dropColumn('is_receipt');
            $table->dropColumn('is_kitchen');
            $table->dropColumn('is_sticker');
        });
    }
}
