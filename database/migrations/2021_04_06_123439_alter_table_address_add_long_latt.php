<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddressAddLongLatt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_addresses', function (Blueprint $table) {
            $table->string('longitude')->nullable(true)->after('country');
            $table->string('latitude')->nullable(true)->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_addresses', function (Blueprint $table) {
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
        });
    }
}
