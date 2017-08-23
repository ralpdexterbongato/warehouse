<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRVDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RVDetails', function (Blueprint $table) {
            $table->increments('id');
            $table->char('RVNo',7);
            $table->string('Particulars',100);
            $table->char('Unit',20);
            $table->decimal('Quantity',18,0);
            $table->string('Remarks',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RVDetails');
    }
}
