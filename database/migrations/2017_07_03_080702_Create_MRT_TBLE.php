<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMRTTBLE extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MRTMaster', function (Blueprint $table) {
            $table->increments('id');
            $table->char('MRTNo',7);
            $table->char('MCTNo',7);
            $table->date('ReturnDate')->nullable();
            $table->string('Particulars',100)->nullable();
            $table->string('AddressTo',50)->nullable();
            $table->string('Returnedby',50)->nullable();
            $table->string('Receivedby',50)->nullable();
            $table->string('Remarks',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MRTMaster');
    }
}
