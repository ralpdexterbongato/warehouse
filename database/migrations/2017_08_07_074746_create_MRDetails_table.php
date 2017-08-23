<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMRDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MRDetails', function (Blueprint $table) {
            $table->increments('id');
            $table->char('MRNo')->unsigned();
            $table->decimal('Quantity',18,0)->nullable();
            $table->string('Unit',50)->nullable();
            $table->string('NameDescription',100)->nullable();
            $table->string('PropertyNo',20)->nullable();
            $table->decimal('UnitValue',18,2)->nullable();
            $table->decimal('TotalValue',18,2)->nullable();
            $table->string('Remarks',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MRDetails');
    }
}
