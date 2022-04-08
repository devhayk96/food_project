<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableProductAddKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->dropColumn('description_1');
            $table->string('status')
                ->change()
                ->after('price')
                ->default('in_stock');
            $table->boolean('is_blocked')
                ->after('status')
                ->default(0);
            $table->boolean('is_various')
                ->after('is_blocked')
                ->default(0);
            $table->boolean('is_open_number')
                ->after('is_various')
                ->default(0);
            $table->boolean('is_open_price')
                ->after('is_open_number')
                ->default(0);
            $table->boolean('restock')
                ->after('is_open_price')
                ->default(0);
            $table->unsignedDecimal('course')
                ->after('restock')
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
            $table->dropColumn('course');
            $table->dropColumn('restock');
            $table->dropColumn('is_open_price');
            $table->dropColumn('is_open_number');
            $table->dropColumn('is_various');
            $table->dropColumn('is_blocked');
            $table->dropColumn('status');
        });
        Schema::table('poshub_products', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'deleted'])
                ->after('price')
                ->default('active');
            $table->text('description_1')
                ->after('image')
                ->nullable(true);
        });
    }
}
