<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->integer('city')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->string('zipcode')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email');
            $table->boolean('verified')->default(false);
            $table->text('logo_path')->nullable();
            $table->text('cover_path')->nullable();
            $table->boolean('vstatus')->default(false);
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
        Schema::dropIfExists('vendors');
    }
}
