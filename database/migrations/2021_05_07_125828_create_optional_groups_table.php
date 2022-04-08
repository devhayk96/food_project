<?php

use Illuminate\Database\Migrations\Migration;
use App\Database\Blueprint;
use App\Database\Schema;

class CreateOptionalGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poshub_optional_groups', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable(true);
            $table->string('name');
            $table->text('description')->nullable(true);
            $table->string('image')->nullable(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('no_discount')->default(true);
            $table->boolean('is_optional')->default(false);
            $table->text('remarks')->nullable(true);
            $table->string('type')->nullable(true);
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
        Schema::dropIfExists('poshub_optional_groups');
    }
}
