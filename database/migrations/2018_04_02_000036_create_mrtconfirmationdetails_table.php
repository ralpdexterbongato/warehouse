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
    public $set_schema_table = 'mrtconfirmationdetails';

    /**
     * Run the migrations.
     * @table mrtconfirmationdetails
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
            $table->char('MRTNo', 7);
            $table->string('Description', 100)->nullable()->default(null);
            $table->decimal('UnitCost', 18, 2)->nullable()->default(null);
            $table->bigInteger('Quantity')->nullable()->default(null);
            $table->string('Unit', 191)->nullable()->default(null);
            $table->decimal('Amount', 18, 2)->nullable()->default(null);

            $table->index(["MRTNo"], 'FK_MRTConfirmationDetails_MRTMaster');


            $table->foreign('MRTNo', 'FK_MRTConfirmationDetails_MRTMaster')
                ->references('MRTNo')->on('mrtmaster')
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
