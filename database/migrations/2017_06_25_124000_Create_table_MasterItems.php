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
          $table->increments('id');
          $table->string('AccountCode',20);
          $table->string('Description',100)->nullable();
          $table->string('Unit',20)->nullable();
          $table->string('ItemCode_id',20);
          $table->decimal('CurrentQuantity',18,0);
          $table->decimal('AlertIfBelow',18,0)->nullable();
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
