<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->boolean('monday')->default(true);
            $table->time('monday_start')->nullable();
            $table->time('monday_end')->nullable();
            $table->boolean('tuesday')->default(true);
            $table->time('tuesday_start')->nullable();
            $table->time('tuesday_end')->nullable();
            $table->boolean('wednesday')->default(true);
            $table->time('wednesday_start')->nullable();
            $table->time('wednesday_end')->nullable();
            $table->boolean('thursday')->default(true);
            $table->time('thursday_start')->nullable();
            $table->time('thursday_end')->nullable();
            $table->boolean('friday')->default(true);
            $table->time('friday_start')->nullable();
            $table->time('friday_end')->nullable();
            $table->boolean('saturday')->default(true);
            $table->time('saturday_start')->nullable();
            $table->time('saturday_end')->nullable();
            $table->boolean('sunday')->default(true);
            $table->time('sunday_start')->nullable();
            $table->time('sunday_end')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('work_hours');
    }
}
