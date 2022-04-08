<?php

use App\Models\Common;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->index()->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->unsignedInteger('country_id')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->index()->nullable()->after('country_id');
            }
            if (!Schema::hasColumn('users', 'zip')) {
                $table->string('zip')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'address_1')) {
                $table->string('address_1')->nullable()->after('zip');
            }
            if (!Schema::hasColumn('users', 'address_2')) {
                $table->string('address_2')->nullable()->after('address_1');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->index()->default(Common::STATUS_ACTIVE)->after('address_2');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->index()->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->index()->nullable()->after('last_login_ip');
            }

            if (Schema::hasColumn('countries', 'id')) {
                $table->foreign('country_id')
                    ->references('id')
                    ->on('countries')
                    ->onDelete('set null');
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
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('users', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'address_2')) {
                $table->dropColumn('address_2');
            }
            if (Schema::hasColumn('users', 'address_1')) {
                $table->dropColumn('address_1');
            }
            if (Schema::hasColumn('users', 'zip')) {
                $table->dropColumn('zip');
            }
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('users', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
}
