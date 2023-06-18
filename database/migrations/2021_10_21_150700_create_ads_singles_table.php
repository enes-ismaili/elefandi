<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsSinglesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_singles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ads_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('dimage')->nullable();
            $table->string('mimage')->nullable();
            $table->string('link')->nullable();
            $table->integer('view')->default(0);
            $table->integer('click')->default(0);
            $table->decimal('fview')->default(0);
            $table->decimal('fclick')->default(0);
            $table->integer('fvaction')->default(0);
            $table->integer('fcaction')->default(0);
            $table->tinyInteger('astatus')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_singles');
    }
}
