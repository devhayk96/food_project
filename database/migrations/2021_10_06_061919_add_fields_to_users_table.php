<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if(!Schema::hasColumn('users', 'language')){
                $table->string('language')->nullable();
            }
            if(!Schema::hasColumn('users', 'number_of_lines')){
                $table->string('number_of_lines')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if(!Schema::hasColumn('users', 'language')){
                $table->dropColumn('language');
            }
            if(!Schema::hasColumn('users', 'number_of_lines')){
                $table->dropColumn('number_of_lines');
            }
        });
    }
}
