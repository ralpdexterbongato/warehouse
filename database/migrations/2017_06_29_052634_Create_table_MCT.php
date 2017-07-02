<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMCT extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MCTMaster', function (Blueprint $table) {
            $table->increments('id');
            $table->char('MCTNo',7);
            $table->char('MIRSNo',7);
            $table->date('MIRSDate')->nullable();
            $table->string('Particulars',150)->nullable();
            $table->string('AddressTo',100)->nullable();
            $table->string('Issuedby',50)->nullable();
            $table->string('Recievedby',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MCTMaster');
    }
}
