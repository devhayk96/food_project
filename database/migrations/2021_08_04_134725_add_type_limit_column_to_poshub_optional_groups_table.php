<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeLimitColumnToPoshubOptionalGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_optional_groups', function (Blueprint $table) {
            $table->integer('type_limit')->nullable()->default(1)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_optional_groups', function (Blueprint $table) {
            $table->dropColumn('type_limit');
        });
    }
}
