<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableVatAddKeyCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poshub_vats', function (Blueprint $table) {
            $table->string('vat_code')
                ->nullable(true)
                ->after('name');
            $table->boolean('is_blocked')
                ->after('is_purchase')
                ->default(0);
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poshub_vats', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'deleted'])
                ->after('is_purchase')
                ->default('active');
            $table->dropColumn('is_blocked');
            $table->dropColumn('vat_code');
        });
    }
}
