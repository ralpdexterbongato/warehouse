<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PurchaseOrderMasters', function (Blueprint $table) {
          $table->char('PONo',7)->primary();
          $table->char('RVNo',7);
          $table->string('Supplier',50);
          $table->string('Address',150)->nullable();
          $table->char('Telephone',15)->nullable();
          $table->string('Purpose',100)->nullable();
          $table->string('GeneralManager',50)->nullable();
          $table->string('GeneralManagerSignature',150)->nullable();
          $table->string('IfDeclined')->nullable();
          $table->date('RVDate')->nullable();
          $table->date('PODate')->nullable();
          $table->string('ApprovalReplacer',50)->nullable();
          $table->string('ApprovalReplacerSignature',150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PurchaseOrderMasters');
    }
}
