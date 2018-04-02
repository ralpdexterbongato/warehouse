<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */

    /**
     * Run the migrations.
     * @table units
     *
     * @return void
     */
    public function up()
    {
      Schema::create('Units', function (Blueprint $table) {
        $table->increments('id');
        $table->char('UnitName', 20);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('Units');
     }
}
