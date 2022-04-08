<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('subcategory_id')
                ->nullable(false);
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('poshub_subcategories')
                ->onDelete('cascade');

            $table->unsignedBigInteger('vat_id')
                ->nullable(true);
            $table->foreign('vat_id')
                ->references('id')
                ->on('poshub_vats')
                ->onDelete('cascade');

            $table->string('article_number')
                ->nullable(true);

            $table->string('name');

            $table->text('image')
                ->nullable(true);

            $table->text('description_1')
                ->nullable(true);

            $table->text('description_2')
                ->nullable(true);

            $table->text('description_3')
                ->nullable(true);

            $table->boolean('main_product')
                ->default(1);

            $table->string('price')
                ->nullable(true);

            $table->enum('status', ['active', 'inactive', 'deleted'])
                ->default('active');

            $table->unsignedBigInteger('created_by')
                ->nullable(false);
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('updated_by')
                ->nullable(true);
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_products');
    }
}
