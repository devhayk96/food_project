<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_orders', function (Blueprint $table) {
            $table->uuid('id')
                ->primary();

            $table->integer('order_number', false, true)
                ->nullable();

            $table->foreignId('order_status_id')
                ->nullable(false)
                ->index();
            $table->foreignId('order_source_id')
                ->nullable(false);
            $table->foreignId('order_type_id')
                ->nullable(false)
                ->index();
            $table->foreignId('shop_id')
                ->nullable(false)
                ->index();
            $table->foreignId('customer_id')
                ->nullable(false);
            $table->foreignId('delivery_id')
                ->nullable();
            $table->foreignId('payment_method_id')
                ->nullable(false);

            $table->boolean('is_asap')
                ->default(false)
                ->nullable();

            $table->timestamp('order_datetime', 0)
                ->nullable();

            $table->timestamp('requested_time', 0)
                ->nullable()
                ->comment('The expected delivery or pickup time.');

            $table->decimal('total_discount')
                ->default(0)
                ->nullable(false);
            $table->decimal('total_price')
                ->nullable(false);
            $table->boolean('is_paid')
                ->nullable(false);

            $table->text('order_remark')
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
//            $table->timestamp('thuisbezorgd_order_date', 0)
//                ->nullable();
            $table->string('thuisbezorgd_public_reference', 256)
                ->nullable()
                ->index();
            $table->decimal('thuisbezorgd_pays_with')
                ->nullable();

            $table->string('ubereats_id', 256)
                ->nullable()
                ->index();

            $table->defineAsEntity();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_orders');
    }
}
