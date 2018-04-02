<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtconfirmationdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'MRTConfirmationDetails';

    /**
     * Run the migrations.
     * @table mrtconfirmationdetails
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MRTConfirmationDetails', function (Blueprint $table) {
        $table->increments('id');
        $table->string('ItemCode', 20)->nullable()->default(null);
        $table->string('AccountCode', 20)->nullable()->default(null);
        $table->char('MRTNo', 7);
        $table->string('Description', 100)->nullable()->default(null);
        $table->decimal('UnitCost', 18, 2)->nullable()->default(null);
        $table->bigInteger('Quantity')->nullable()->default(null);
        $table->string('Unit', 191)->nullable()->default(null);
        $table->decimal('Amount', 18, 2)->nullable()->default(null);
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
