<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCanvassMasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CanvassMasters', function (Blueprint $table) {
          $table->increments('id');
          $table->char('RVNo',7);
          $table->string('Supplier',50);
          $table->string('Address',150)->nullable();
          $table->char('Telephone',15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CanvassMasters');
    }
}
