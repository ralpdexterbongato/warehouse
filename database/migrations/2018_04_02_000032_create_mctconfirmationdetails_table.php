<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMctconfirmationdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'MCTConfirmationdetails';

    /**
     * Run the migrations.
     * @table mctconfirmationdetails
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('ItemCode', 20)->nullable()->default(null);
            $table->string('AccountCode', 20)->nullable()->default(null);
            $table->char('MCTNo', 7);
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
