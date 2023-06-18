<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->default(0);
            $table->tinyInteger('ntype')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->string('fields')->nullable();
            $table->tinyInteger('nactive')->default(1);
            $table->tinyInteger('nshow')->default(1);
            $table->tinyInteger('nsent')->default(0);
            $table->string('oneid')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
