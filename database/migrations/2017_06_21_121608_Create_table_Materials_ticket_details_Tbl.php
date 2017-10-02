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
          $table->string('ItemCode',20)->index();
          $table->string('MTType',5);
          $table->string('MTNo',7);
          $table->string('AccountCode',20)->nullable();
          $table->decimal('UnitCost',18,2)->nullable();
          $table->decimal('Quantity',18,0)->nullable();
          $table->decimal('RRQuantityDelivered',18,0)->nullable();
          $table->decimal('Amount',18,2)->nullable();
          $table->decimal('CurrentCost',18,2)->nullable();
          $table->decimal('CurrentQuantity',18,0)->nullable();
          $table->decimal('CurrentAmount',18,2)->nullable();
          $table->date('MTDate');
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
