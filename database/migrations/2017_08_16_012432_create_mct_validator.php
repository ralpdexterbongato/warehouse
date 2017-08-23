<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMctValidator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MCTValidators', function (Blueprint $table) {
          $table->increments('id');
          $table->char('MIRSNo',9);
          $table->string('ItemCode',20);
          $table->string('Particulars',150)->nullable();
          $table->string('Unit',50)->nullable();
          $table->decimal('Quantity',18,0)->nullable();
          $table->string('Remarks',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MCTValidators');
    }
}
