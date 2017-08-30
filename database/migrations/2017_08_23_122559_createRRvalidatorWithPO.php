<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRRvalidatorWithPO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RRValidatorWithPO', function (Blueprint $table) {
          $table->increments('id');
          $table->decimal('Price',18,2)->nullable();
          $table->string('Unit',10);
          $table->string('Description',100);
          $table->decimal('Qty',18,0);
          $table->decimal('Amount',18,2);
          $table->char('PONo',7);
          $table->string('ItemCode',20)->nullable();
          $table->string('AccountCode',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RRValidatorWithPO');
    }
}
