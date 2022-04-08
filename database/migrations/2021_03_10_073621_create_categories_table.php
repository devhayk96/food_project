<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_categories', function (Blueprint $table) {
            $table->id();

            $table->string('article_number')
                ->nullable(true);

            $table->string('name');

            $table->text('image')
                ->nullable(true);

            $table->text('description')
                ->nullable(true);

            $table->string('kitchen_1_id')
                ->nullable(true);

            $table->string('kitchen_2_id')
                ->nullable(true);

            $table->string('kitchen_3_id')
                ->nullable(true);

            $table->boolean('is_blocked')
                ->default(0);

            $table->boolean('is_food')
                ->default(0);

            $table->boolean('is_drink')
                ->default(0);

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
        Schema::dropIfExists('poshub_categories');
    }
}
