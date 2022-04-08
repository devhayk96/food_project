<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPiggySettingsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_settings', function (Blueprint $table) {
            $table->string('logo_width')->default('127')->nullable();
            $table->string('logo_height')->default('127')->nullable();
            $table->string('logo_original_width')->nullable();
            $table->string('logo_original_height')->nullable();
            $table->string('tabs_header_color')->nullable();
            $table->string('header_background_color')->nullable();
            $table->boolean('piggy_checkbox')->default(false)->nullable();
            $table->text('piggy_secret_token')->nullable();
            $table->text('piggy_client_id')->nullable();
            $table->text('piggy_shop_id')->nullable();
            $table->text('editor_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_settings', function (Blueprint $table) {
            //
        });
    }
}
