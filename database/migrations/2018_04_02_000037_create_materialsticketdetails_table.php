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
    public $set_schema_table = 'materialsticketdetails';

    /**
     * Run the migrations.
     * @table materialsticketdetails
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
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
            $table->date('MTDate');
            $table->smallInteger('IsRollBack')->nullable()->default(null);

            $table->index(["ItemCode"], 'materialsticketdetails_itemcode_index');


            $table->foreign('ItemCode', 'materialsticketdetails_itemcode_index')
                ->references('ItemCode')->on('masteritems')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
