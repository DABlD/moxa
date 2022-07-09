<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger("user_id");
            $table->unsignedSmallInteger("category_id");

            $table->string("image")->default('images/default_medicine_avatar');
            $table->string("code");
            $table->string("brand");
            $table->string("name");
            $table->string("packaging");
            $table->float("unit_price", 8, 2);
            $table->float("cost_price", 8, 2);
            $table->unsignedSmallInteger("stock")->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicines');
    }
}
