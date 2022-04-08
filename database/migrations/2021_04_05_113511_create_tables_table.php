<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_tables', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shop_id')
                ->nullable(false);
            $table->foreign('shop_id')
                ->references('id')
                ->on('poshub_shops')
                ->onDelete('cascade');

            $table->string('number')->nullable();
            $table->string('name');

            $table->boolean('is_blocked')
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
        Schema::dropIfExists('poshub_tables');
    }
}
