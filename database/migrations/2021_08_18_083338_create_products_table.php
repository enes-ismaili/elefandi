<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('sku')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('category_id');
            $table->text('image')->nullable();
            $table->string('weight')->nullable();
            $table->string('size')->nullable();
            $table->string('personalize')->nullable();
            $table->double('price')->nullable();
            $table->integer('stock')->nullable();
            $table->text('colors')->nullable();
            $table->text('attributes')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('vstatus')->default(true);
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
        Schema::dropIfExists('products');
    }
}
