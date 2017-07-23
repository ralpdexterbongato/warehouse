<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRRConfirmationsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RRConfirmationDetails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ItemCode',20)->index();
            $table->string('RRNo',7);
            $table->string('AccountCode',20)->nullable();
            $table->string('Description',100)->nullable();
            $table->decimal('UnitCost',18,2)->nullable();
            $table->integer('RRQuantityDelivered')->nullable();
            $table->decimal('QuantityAccepted',18,0)->nullable();
            $table->string('Unit')->nullable();
            $table->decimal('Amount',18,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RRConfirmationDetails');
    }
}
