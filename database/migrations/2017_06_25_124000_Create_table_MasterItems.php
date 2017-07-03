<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMasterItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MasterItems', function (Blueprint $table) {
          //$table->engine = 'InnoDB';
          $table->increments('id');
          $table->string('AccountCode',20);
          $table->string('Description',100)->nullable();
          $table->string('Unit')->nullable();
          $table->decimal('UnitCost',18,2)->nullable();
          $table->decimal('Quantity',18,0)->nullable();
          $table->string('Month',50)->nullable();
          $table->string('ItemCode_id',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MasterItems');
    }
}
