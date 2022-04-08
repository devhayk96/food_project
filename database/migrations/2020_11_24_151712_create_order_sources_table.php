<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOrderSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_source_type_id')
                ->nullable(false)
                ->index();
            $table->string('code', 32)
                ->nullable(false)
                ->unique();
            $table->string('name', 256)
                ->nullable(false)
                ->comment('The name of the order source in a human readable format.');
            $table->boolean('is_active')
                ->nullable(false)
                ->default(false)
                ->index();
            $table->boolean('is_auto_accept')
                ->nullable(false)
                ->default(false);
            $table->text('credentials')
                ->nullable(false)
                ->comment('This is an object of the type ClientCredentialsInterface');
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
        Schema::dropIfExists('poshub_order_sources');
    }
}
