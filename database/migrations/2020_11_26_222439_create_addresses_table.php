<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

/**
 * Class CreateAddressesTable
 *
 * @todo: to be implemented:
 *                  + Country country
 *                  + countryFields[] fields
 *                  + string fingerprint
 */
class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street', 512)
                ->nullable(false)
                ->index();
            $table->string('street_extra', 512)
                ->nullable();
            $table->string('postcode', 16)
                ->nullable(false)
                ->index();
            $table->string('city', 256)
                ->nullable();
            $table->string('country', 256)
                ->nullable();
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
        Schema::dropIfExists('poshub_addresses');
    }
}
