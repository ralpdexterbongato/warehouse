<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableItemMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ItemMasters', function (Blueprint $table) {
          $table->increments('id');
          $table->string('AccountCode');
          $table->string('ItemCode');
          $table->string('Description')->nullable();
          $table->string('Unit')->nullable();
          $table->decimal('UnitCost',18,2)->nullable();
          $table->decimal('Quantity',18,0)->nullable();
          $table->integer('MaterialsTicketDetails_id');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ItemMasters');
    }
}
