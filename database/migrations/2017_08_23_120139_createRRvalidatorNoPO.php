<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRRvalidatorNoPO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RRValidatorNoPO', function (Blueprint $table) {
          $table->increments('id');
          $table->char('RVNo',7);
          $table->string('Particulars',100);
          $table->char('Unit',20);
          $table->decimal('Quantity',18,0);
          $table->string('Remarks',100)->nullable();
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
        Schema::dropIfExists('RRValidatorNoPO');
    }
}