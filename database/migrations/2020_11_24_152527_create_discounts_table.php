<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)
                ->nullable(false)
                ->unique()
                ->comment('The discount code, can be used for validation purpose, it is an unique index.');
            $table->string('name', 256)
                ->nullable(false)
                ->comment('The discount name, can be used for validation purpose.');
            $table->decimal('value')
                ->nullable(false);
            $table->boolean('is_active')
                ->nullable(false)
                ->comment('If is false, the discount cannot be used.');
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
        Schema::dropIfExists('poshub_discounts');
    }
}
