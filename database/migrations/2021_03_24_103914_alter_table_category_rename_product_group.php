<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCategoryRenameProductGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::rename('poshub_categories', 'poshub_product_groups');

        Schema::table('poshub_product_groups', function (Blueprint $table) {
            $table->renameColumn('article_number','number');
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
        Schema::table('poshub_product_groups', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'deleted'])
                ->after('is_drink')
                ->default('active');
            $table->renameColumn('number','article_number');
        });

        Schema::rename('poshub_product_groups', 'poshub_categories');
    }
}
