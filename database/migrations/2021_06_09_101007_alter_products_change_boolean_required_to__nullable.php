<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsChangeBooleanRequiredToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->boolean('main_product')
                ->nullable(true)
                ->change();
            $table->boolean('is_various')
                ->nullable(true)
                ->change();
            $table->boolean('restock')
                ->nullable(true)
                ->change();
            $table->boolean('is_open_price')
                ->nullable(true)
                ->change();
            $table->boolean('is_open_number')
                ->nullable(true)
                ->change();
            $table->boolean('is_receipt')
                ->nullable(true)
                ->change();
            $table->boolean('is_kitchen')
                ->nullable(true)
                ->change();
            $table->boolean('is_sticker')
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
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->boolean('main_product')
                ->nullable(false)
                ->change();
            $table->boolean('is_various')
                ->nullable(false)
                ->change();
            $table->boolean('restock')
                ->nullable(false)
                ->change();
            $table->boolean('is_open_price')
                ->nullable(false)
                ->change();
            $table->boolean('is_open_number')
                ->nullable(true)
                ->change();
            $table->boolean('is_receipt')
                ->nullable(false)
                ->change();
            $table->boolean('is_kitchen')
                ->nullable(false)
                ->change();
            $table->boolean('is_sticker')
                ->nullable(false)
                ->change();
        });
    }
}
