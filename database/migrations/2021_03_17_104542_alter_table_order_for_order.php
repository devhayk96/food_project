<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderForOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_orders', function (Blueprint $table) {

//            dropping columns
            $table->dropColumn('requested_products');
            $table->dropColumn('requested_discounts');
            $table->dropColumn('original_json');
            $table->dropColumn('thuisbezorgd_id');
            $table->dropColumn('thuisbezorgd_order_key');
            $table->dropColumn('thuisbezorgd_platform');
            $table->dropColumn('thuisbezorgd_public_reference');
            $table->dropColumn('thuisbezorgd_pays_with');
            $table->dropColumn('delivery_id');

//                create columns
            $table->unsignedDouble('delivery_cost')->after('delivery_id')->default(0);
            $table->text('delivery_remarks')->after('delivery_cost')->nullable(true);
            $table->unsignedDouble('tip_price')->after('total_price')->default(0);
            $table->json('order_json')->after('order_remark')->nullable(true);
            $table->json('fulfillment_issues')->after('order_json')->nullable(true);
            $table->timestamp('actual_delivery_time', 0)->after('requested_time')->nullable(true);

            $table->json('customer_json')->after('order_json')->nullable(true);
            $table->json('address_json')->after('customer_json')->nullable(true);

            $table->unsignedBigInteger('courier_type_id')
                ->after('customer_id')
                ->nullable(true);
            $table->foreign('courier_type_id')
                ->references('id')
                ->on('poshub_courier_types')
                ->onDelete('cascade');

            $table->foreign('order_status_id')
                ->references('id')
                ->on('poshub_order_statuses')
                ->onDelete('cascade');

            $table->foreign('order_source_id')
                ->references('id')
                ->on('poshub_order_source_shops')
                ->onDelete('cascade');

            $table->foreign('order_type_id')
                ->references('id')
                ->on('poshub_order_types')
                ->onDelete('cascade');

            $table->foreign('shop_id')
                ->references('id')
                ->on('poshub_shops')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')
                ->on('poshub_customers')
                ->onDelete('cascade');

            $table->foreign('payment_method_id')
                ->references('id')
                ->on('poshub_payment_methods')
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
        Schema::table('poshub_orders', function (Blueprint $table) {
            $table->dropColumn('actual_delivery_time');
            $table->dropColumn('fulfillment_issues');
            $table->dropColumn('order_json');
            $table->dropColumn('customer_json');
            $table->dropColumn('address_json');
            $table->dropColumn('tip_price');
            $table->dropColumn('delivery_remarks');
            $table->dropColumn('delivery_cost');

            $table->dropForeign(['courier_type_id']);
            $table->dropColumn('courier_type_id');

            $table->dropForeign(['order_status_id']);
            $table->dropForeign(['order_source_id']);
            $table->dropForeign(['order_type_id']);
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['payment_method_id']);



            $table->foreignId('delivery_id')
                ->nullable();

            $table->text('requested_products')
                ->nullable(false);

            $table->text('requested_discounts')
                ->nullable();

            $table->text('original_json')
                ->nullable();

            $table->string('thuisbezorgd_id', 256)
                ->nullable()
                ->index();
            $table->string('thuisbezorgd_order_key', 256)
                ->nullable()
                ->index();
            $table->string('thuisbezorgd_platform', 256)
                ->nullable()
                ->index();
            $table->string('thuisbezorgd_public_reference', 256)
                ->nullable()
                ->index();
            $table->decimal('thuisbezorgd_pays_with')
                ->nullable();
        });
    }
}
