<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateMenuCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_menu_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->text('description')
                ->nullable(true);

            $table->string('image')
                ->nullable(true);

            $table->boolean('is_blocked')
                ->default(0);

            $table->boolean('is_food')
                ->default(0);

            $table->boolean('is_drink')
                ->default(0);

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
        Schema::dropIfExists('poshub_menu_categories');
    }
}
