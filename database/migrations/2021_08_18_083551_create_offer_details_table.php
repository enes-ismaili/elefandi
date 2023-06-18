<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('vendor_id');
            $table->tinyInteger('type');
            $table->unsignedBigInteger('prod_id');
            $table->unsignedBigInteger('variant_id')->default(0);
            $table->tinyInteger('action')->default(1);
            $table->decimal('discount');
            $table->tinyInteger('cactive')->default(1);

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
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
        Schema::dropIfExists('offer_details');
    }
}
