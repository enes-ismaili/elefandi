<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('code');
            $table->string('ucode');
            $table->text('description')->nullable();
            $table->json('categories')->nullable();
            $table->json('products')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('action')->default(1);
            $table->decimal('discount')->nullable();
            $table->tinyInteger('withoffer')->default(1);
            $table->timestamp('start_date');
            $table->timestamp('expire_date');
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
        Schema::dropIfExists('coupons');
    }
}
