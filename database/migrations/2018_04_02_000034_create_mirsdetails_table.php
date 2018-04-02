<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMirsdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'mirsdetails';

    /**
     * Run the migrations.
     * @table mirsdetails
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->char('MIRSNo', 7);
            $table->string('ItemCode', 20);
            $table->string('Particulars', 150)->nullable()->default(null);
            $table->string('Unit', 50)->nullable()->default(null);
            $table->bigInteger('Quantity');
            $table->string('Remarks', 100)->nullable()->default(null);
            $table->bigInteger('QuantityValidator')->nullable()->default(null);

            $table->index(["MIRSNo"], 'FK_MIRSDetails_MIRSMaster');


            $table->foreign('MIRSNo', 'FK_MIRSDetails_MIRSMaster')
                ->references('MIRSNo')->on('mirsmaster')
                ->onDelete('no action')
                ->onUpdate('no action');
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
