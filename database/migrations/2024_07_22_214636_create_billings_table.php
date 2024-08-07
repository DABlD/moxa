<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->integer('moxa_id');

            $table->date('from');
            $table->date('to');

            $table->double('reading');
            $table->double('late_interest');
            $table->double('rate');
            $table->double('total');

            $table->string('status');

            $table->string('mop')->nullable();
            $table->string('refno')->nullable();
            $table->string('invoice')->nullable();
            $table->datetime('date_paid')->nullable();

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
        Schema::dropIfExists('billings');
    }
}
