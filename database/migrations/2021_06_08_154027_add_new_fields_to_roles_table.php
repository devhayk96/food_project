<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'created_by_id')) {
                $table->unsignedBigInteger('created_by_id')->nullable()->after('description');

                if (Schema::hasColumn('users', 'id')) {
                    $table->foreign('created_by_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                }
            }
            if (!Schema::hasColumn('roles', 'updated_by_id')) {
                $table->unsignedBigInteger('updated_by_id')->nullable()->after('created_by_id');

                if (Schema::hasColumn('users', 'id')) {
                    $table->foreign('updated_by_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                }
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
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'updated_by_id')) {
                $table->dropForeign(['updated_by_id']);
                $table->dropColumn('updated_by_id');
            }
            if (Schema::hasColumn('roles', 'created_by_id')) {
                $table->dropForeign(['created_by_id']);
                $table->dropColumn('created_by_id');
            }
        });
    }
}
