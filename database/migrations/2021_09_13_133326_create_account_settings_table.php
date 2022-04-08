<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo');
            $table->string('background_image')->nullable();
            $table->string('background_color')->default('');
            $table->string('button_color')->default('');
            $table->string('icon_color')->default('');
            $table->string('text_color')->default('');
            $table->string('link_color')->default('');
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
        Schema::dropIfExists('account_settings');
    }
}
