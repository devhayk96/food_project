<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AddPinCodeColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->mediumInteger('pin_code_length')
                ->default(User::DEFAULT_PIN_CODE_LENGTH)
                ->after('password');
            $table->integer('pin_code')->unique()->nullable()->after('password');
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
            $table->dropColumn('pin_code_length');
            $table->dropColumn('pin_code');
        });
    }
}
