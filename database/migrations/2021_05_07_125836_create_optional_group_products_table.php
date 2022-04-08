<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOptionalGroupProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_optional_group_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('optional_group_id');
            $table->foreign('optional_group_id')
                ->references('id')
                ->on('poshub_optional_groups')
                ->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('poshub_products')
                ->onDelete('cascade');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('poshub_optional_group_products');
    }
}
