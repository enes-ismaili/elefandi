<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('manage_users')->default(false);
            $table->boolean('manage_vendors')->default(false);
            $table->boolean('manage_products')->default(false);
            $table->boolean('manage_orders')->default(false);
            $table->boolean('manage_supports')->default(false);
            $table->boolean('manage_stories')->default(false);
            $table->boolean('manage_ads')->default(false);
            $table->boolean('manage_notifications')->default(false);
            $table->boolean('delete_rights')->default(false);
            $table->boolean('can_edit')->default(true);
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
        Schema::dropIfExists('roles');
    }
}
