<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_currencies', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('short_name');

            $table->string('symbol');

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
        Schema::dropIfExists('poshub_currencies');
    }
}
