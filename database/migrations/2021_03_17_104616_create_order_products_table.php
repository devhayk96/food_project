<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('poshub_products')
                ->onDelete('cascade');

            $table->char('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('poshub_orders')
                ->onDelete('cascade');

            $table->unsignedDouble('quantity');

            $table->unsignedDouble('unit_price');

            $table->unsignedDouble('total_price');

            $table->text('remarks')
                ->nullable(true);

            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('updated_by_id')->nullable(true);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('created_by_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('updated_by_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('poshub_order_products');
    }
}
