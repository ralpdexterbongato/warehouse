<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'RVDetails';

    /**
     * Run the migrations.
     * @table rvdetails
     *
     * @return void
     */
    public function up()
    {
      Schema::create('RVDetails', function (Blueprint $table) {
        $table->increments('id');
        $table->char('RVNo', 7);
        $table->string('Particulars', 100);
        $table->char('Unit', 20);
        $table->bigInteger('Quantity');
        $table->string('Remarks', 100)->nullable()->default(null);
        $table->string('ItemCode', 20)->nullable()->default(null);
        $table->string('AccountCode', 20)->nullable()->default(null);
        $table->bigInteger('QuantityValidator')->nullable()->default(null);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->set_schema_table);
     }
}
