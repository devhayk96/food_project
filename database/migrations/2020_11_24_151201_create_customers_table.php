<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

/**
 * Class CreateCustomersTable
 *
 * @todo: implement the default address and the latestConsent relations
 */
class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)
                ->nullable(false)
                ->index()
                ->comment('The name and surname of the customer');
            $table->string('company', 256)
                ->nullable()
                ->index()
                ->comment('The company name of the customer');
            $table->string('phone', 64)
                ->nullable()
                ->index()
                ->comment('The telephone of the customer');
            $table->string('email', 256)
                ->nullable()
                ->index()
                ->comment('The email of the customer');
            $table->string('note', 1024)
                ->nullable();
            $table->defineAsEntity();

//            $table->index(['name', 'phone', 'email']);
//            $table->index(['name', 'phone']);
//            $table->index(['name', 'email']);
//            $table->index(['phone', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poshub_customers');
    }
}
