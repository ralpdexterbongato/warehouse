<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsticketdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'MaterialsTicketDetails';

    /**
     * Run the migrations.
     * @table materialsticketdetails
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MaterialsTicketDetails', function (Blueprint $table) {
        $table->increments('id');
        $table->string('ItemCode', 20);
        $table->string('MTType', 5);
        $table->string('MTNo', 7);
        $table->string('AccountCode', 20)->nullable()->default(null);
        $table->decimal('UnitCost', 18, 2)->nullable()->default(null);
        $table->bigInteger('Quantity')->nullable()->default(null);
        $table->decimal('Amount', 18, 2)->nullable()->default(null);
        $table->decimal('CurrentCost', 18, 2)->nullable()->default(null);
        $table->bigInteger('CurrentQuantity')->nullable()->default(null);
        $table->decimal('CurrentAmount', 18, 2)->nullable()->default(null);
        $table->datetime('MTDate');
        $table->smallInteger('IsRollBack')->nullable()->default(null);
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