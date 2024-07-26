<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBillingsAddBillNoColumnInBillings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('billno')->after('moxa_id');
            $table->double('initReading')->after('reading')->nullable();
            $table->double('consumption')->after('initReading')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('billno');
            $table->dropColumn('initReading');
            $table->dropColumn('consumption');
        });
    }
}
