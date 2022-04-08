<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderStatusForOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_order_status_updates', function (Blueprint $table) {
            $table->dropColumn('additional_data');
            $table->json('order_status_update_json')
                ->after('order_status_id')
                ->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_order_status_updates', function (Blueprint $table) {
            $table->dropColumn('order_status_update_json');
            $table->text('additional_data')->after('order_status_id');
        });
    }
}
