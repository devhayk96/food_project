<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)
                ->unique()
                ->comment('The unique code used to bind the group to the token auth.');
            $table->string('name', 256)
                ->index()
                ->comment('The name of the group, used to select it.');
            $table->text('description')
                ->nullable(true)
                ->comment('The description of the group, for informative purpose only.');
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
        Schema::dropIfExists('poshub_groups');
    }
}
