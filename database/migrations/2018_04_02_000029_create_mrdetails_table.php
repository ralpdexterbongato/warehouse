<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'MRDetails';

    /**
     * Run the migrations.
     * @table mrdetails
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MRDetails', function (Blueprint $table) {
        $table->increments('id');
        $table->char('MRNo', 7);
        $table->bigInteger('Quantity')->nullable()->default(null);
        $table->string('Unit', 50)->nullable()->default(null);
        $table->string('NameDescription', 100)->nullable()->default(null);
        $table->string('PropertyNo', 20)->nullable()->default(null);
        $table->decimal('UnitValue', 18, 2)->nullable()->default(null);
        $table->decimal('TotalValue', 18, 2)->nullable()->default(null);
        $table->string('Remarks', 50)->nullable()->default(null);
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
