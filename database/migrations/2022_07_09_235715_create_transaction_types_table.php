<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();

            $table->integer('admin_id')->unsigned();
            $table->string('type');
            $table->string("classification")->nullable();
            $table->string("operator")->nullable();
            $table->integer("demand")->default(1)->nullable();
            $table->integer("late_interest")->nullable();
            $table->boolean('inDashboard')->default(false);
            $table->boolean('canDelete')->default(true);

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
        Schema::dropIfExists('transaction_types');
    }
}
