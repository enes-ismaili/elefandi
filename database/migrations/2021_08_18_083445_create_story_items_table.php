<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('story_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('stories_id');
            $table->string('type')->default('photo');
            $table->integer('length')->default(5);
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->integer('link_type')->default(1);
            $table->integer('link_id')->default(1);
            $table->tinyInteger('corder')->default(99);
            $table->boolean('cactive')->default(false);
            $table->tinyInteger('cview')->default(0);
            $table->tinyInteger('clicks')->default(0);
            $table->timestamp('start_story');
            $table->timestamp('end_story');
            $table->timestamps();

            $table->foreign('stories_id')
                ->references('id')
                ->on('stories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('story_items');
    }
}
