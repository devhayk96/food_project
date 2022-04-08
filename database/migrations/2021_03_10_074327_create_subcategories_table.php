<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_subcategories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id')
                ->nullable(false);
            $table->foreign('category_id')
                ->references('id')
                ->on('poshub_categories')
                ->onDelete('cascade');

            $table->string('article_number')
                ->nullable(true);

            $table->string('name');

            $table->text('image')
                ->nullable(true);

            $table->text('description')
                ->nullable(true);

            $table->boolean('is_blocked')
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
        Schema::dropIfExists('poshub_subcategories');
    }
}
