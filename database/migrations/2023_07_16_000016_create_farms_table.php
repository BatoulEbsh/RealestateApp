<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('number_of_rooms');
            $table->integer('number_of_pools');
            $table->boolean('is_garden');
            $table->boolean('is_bar');
            $table->boolean('is_baby_pool');
            $table->text('description');
            $table->unsignedBigInteger('property_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('farms');
    }
};
