<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderAddKeysFromSheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')
                ->nullable(true)
                ->after('customer_id');
            $table->foreign('address_id')
                ->references('id')
                ->on('poshub_addresses')
                ->onDelete('cascade');

            $table->unsignedBigInteger('extra_cost_vat_id')
                ->nullable(true)
                ->after('total_vat_amount');
            $table->foreign('extra_cost_vat_id')
                ->references('id')
                ->on('poshub_vats')
                ->onDelete('cascade');

            $table->unsignedBigInteger('delivery_vat_id')
                ->nullable(true)
                ->after('delivery_cost');
            $table->foreign('delivery_vat_id')
                ->references('id')
                ->on('poshub_vats')
                ->onDelete('cascade');

            $table->unsignedBigInteger('table_id')
                ->nullable(true)
                ->after('courier_type_id');
            $table->foreign('table_id')
                ->references('id')
                ->on('poshub_tables')
                ->onDelete('cascade');

            $table->unsignedBigInteger('cancellation_id')
                ->nullable(true)
                ->after('table_id');
            $table->foreign('cancellation_id')
                ->references('id')
                ->on('poshub_cancellation_reasons')
                ->onDelete('cascade');

            $table->text('cancellation_comment')->nullable(true)->after('cancellation_id');
            $table->unsignedDecimal('extra_cost_vat_amount')->default(0)->after('extra_cost_vat_id');
            $table->unsignedDecimal('extra_costs')->default(0)->after('total_price');
            $table->boolean('is_printed')->default(0)->after('extra_costs');
            $table->boolean('is_pos_sync')->default(0)->after('fulfillment_issues');
            $table->unsignedDecimal('pays_with')->nullable(true)->after('total_price');
            $table->string('payment_session_id')->nullable(true)->after('payment_method_id');
            $table->unsignedInteger('number_of_guests')->default(0)->after('order_remark');
            $table->string('course')->nullable(true)->after('number_of_guests');
            $table->unsignedDecimal('distance')->nullable(true)->after('is_pos_sync');
            $table->string('invoice_id')->nullable(true)->after('distance');
            $table->dateTime('courier_left_at')->nullable(true)->after('invoice_id');
            $table->dateTime('courier_back_at')->nullable(true)->after('courier_left_at');
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
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
            $table->dropForeign(['extra_cost_vat_id']);
            $table->dropColumn('extra_cost_vat_id');
            $table->dropForeign(['delivery_vat_id']);
            $table->dropColumn('delivery_vat_id');
            $table->dropForeign(['table_id']);
            $table->dropColumn('table_id');
            $table->dropForeign(['cancellation_id']);
            $table->dropColumn('cancellation_id');
            $table->dropColumn('cancellation_comment');
            $table->dropColumn('extra_cost_vat_amount');
            $table->dropColumn('extra_costs');
            $table->dropColumn('is_printed');
            $table->dropColumn('is_pos_sync');
            $table->dropColumn('pays_with');
            $table->dropColumn('payment_session_id');
            $table->dropColumn('number_of_guests');
            $table->dropColumn('course');
            $table->dropColumn('distance');
            $table->dropColumn('invoice_id');
            $table->dropColumn('courier_left_at');
            $table->dropColumn('courier_back_at');
        });
    }
}
