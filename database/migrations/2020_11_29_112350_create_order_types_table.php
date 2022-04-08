<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

/**
 * Class CreateOrderTypesTable
 *
 *  'RESTAURANT'
 *  'TAKEAWAY'
 *  'PICK_UP'
 *  'EAT_IN'
 *  'DELIVERY'
 *
 */
class CreateOrderTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_order_types', function (Blueprint $table) {
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
        Schema::dropIfExists('poshub_order_types');
    }
}
