<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCanvassmastersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */

    /**
     * Run the migrations.
     * @table canvassmasters
     *
     * @return void
     */
      public function up()
      {
        Schema::create('CanvassMasters', function (Blueprint $table) {
          $table->increments('id');
          $table->char('RVNo', 7);
          $table->string('Supplier', 50);
          $table->string('Address', 150)->nullable()->default(null);
          $table->char('Telephone', 15)->nullable()->default(null);
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