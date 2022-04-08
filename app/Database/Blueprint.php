<?php


namespace App\Database;


class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @return void
     */
    public function defineAsEntity() : void
    {
        $this->unsignedBigInteger('created_by_id');
        $this->foreign('created_by_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        $this->unsignedBigInteger('updated_by_id')->nullable(true);
        $this->foreign('updated_by_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        $this->timestamps();
        $this->softDeletes('deleted_at', 0);
    }

    public function defineAsSourceTypeBasedHierarchicalRelationship() : void
    {
        $this->string('code', 64)
            ->nullable(false)
            ->index();
        $this->string('name', 512)
            ->nullable();
        $this->foreignId('source_type_id')
            ->nullable(false)
            ->index();
        $this->foreignId('parent_id')
            ->nullable()
            ->index();
        $this->boolean('is_active')
            ->nullable(false)
            ->default(false)
            ->index();
    }
}
