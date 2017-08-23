<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PurchaseOrderDetails', function (Blueprint $table) {
          $table->increments('id');
          $table->decimal('Price',18,2)->nullable();
          $table->string('Unit',10);
          $table->string('Description',100);
          $table->decimal('Qty',18,0);
          $table->decimal('Amount',18,0);
          $table->char('PurchaseOrderMasters_PONo',7)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PurchaseOrderDetails');
    }
}
