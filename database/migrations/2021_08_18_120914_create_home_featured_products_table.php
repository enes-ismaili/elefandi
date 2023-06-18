<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeFeaturedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_featured_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('button')->nullable();
            $table->string('link')->nullable();
            $table->tinyInteger('corder')->default(99);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('home_featured_products');
    }
}
