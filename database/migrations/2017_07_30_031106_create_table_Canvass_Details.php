<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCanvassDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CanvassDetails', function (Blueprint $table) {
          $table->increments('id');
          $table->decimal('Price',18,2)->nullable();
          $table->string('Unit',10);
          $table->string('Article',100);
          $table->decimal('Qty',18,0);
          $table->integer('CanvassMasters_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CanvassDetails');
    }
}
