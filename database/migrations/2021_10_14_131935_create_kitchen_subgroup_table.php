<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitchenSubgroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_kitchen_subgroup', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kitchen_id');
            $table->foreign('kitchen_id')
                ->references('id')
                ->on('poshub_kitchens')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('subgroup_id');
            $table->foreign('subgroup_id')
                ->references('id')
                ->on('poshub_product_subgroups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('drag_order')->nullable();
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
        Schema::dropIfExists('poshub_kitchen_subgroup');
    }
}
