<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubcategoryRenameProductSubgroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('poshub_subcategories', 'poshub_product_subgroups');

        Schema::table('poshub_product_subgroups', function (Blueprint $table) {
            $table->renameColumn('article_number','number');
            $table->renameColumn('category_id','product_group_id');
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
        Schema::table('poshub_product_subgroups', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'deleted'])
                ->after('is_blocked')
                ->default('active');
            $table->renameColumn('number','article_number');
            $table->renameColumn('product_group_id','category_id');
        });

        Schema::rename('poshub_product_subgroups', 'poshub_subcategories');
    }
}
