<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMaterialsTicketDetailsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MaterialsTicketDetails', function (Blueprint $table) {
          $table->increments('id');
          $table->string('ItemCode');
          $table->string('MTType');
          $table->string('MTNo');
          $table->string('AccountCode')->nullable();
          $table->decimal('UnitCost',18,2)->nullable();
          $table->decimal('Quantity',18,0)->nullable();
          $table->string('Unit')->nullable();
          $table->decimal('Amount',18,2)->nullable();
          $table->decimal('CurrentCost',18,2)->nullable();
          $table->decimal('CurrentQuantity',18,0)->nullable();
          $table->decimal('CurrentAmount',18,2)->nullable();
          $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MaterialsTicketDetails');
    }
}
