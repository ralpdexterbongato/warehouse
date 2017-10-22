<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePODetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PODetails', function (Blueprint $table) {
          $table->increments('id');
          $table->string('ItemCode',20)->nullable();
          $table->string('AccountCode',20)->nullable();
          $table->decimal('Price',18,2)->nullable();
          $table->string('Unit',10);
          $table->string('Description',100);
          $table->decimal('Qty',18,0);
          $table->decimal('Amount',18,2);
          $table->char('PONo',7)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PODetails');
    }
}
