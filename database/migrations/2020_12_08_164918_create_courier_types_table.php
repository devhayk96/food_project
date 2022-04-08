<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateCourierTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_courier_types', function (Blueprint $table) {
            $table->id();
            $table->defineAsSourceTypeBasedHierarchicalRelationship();
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
        Schema::dropIfExists('poshub_courier_types');
    }
}
