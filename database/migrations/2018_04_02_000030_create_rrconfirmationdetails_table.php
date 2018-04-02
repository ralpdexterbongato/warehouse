<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrconfirmationdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'rrconfirmationdetails';

    /**
     * Run the migrations.
     * @table rrconfirmationdetails
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('AccountCode', 20)->nullable()->default(null);
            $table->decimal('Amount', 18, 2)->nullable()->default(null);
            $table->string('Description', 100)->nullable()->default(null);
            $table->string('ItemCode', 20)->nullable()->default(null);
            $table->bigInteger('QuantityAccepted')->nullable()->default(null);
            $table->bigInteger('RRQuantityDelivered')->nullable()->default(null);
            $table->string('Unit', 20)->nullable()->default(null);
            $table->decimal('UnitCost', 18, 2)->nullable()->default(null);
            $table->char('RRNo', 7);
            $table->bigInteger('QuantityValidator')->nullable()->default(null);

            $table->index(["RRNo"], 'FK_RRConfirmationDetails_RRMaster');


            $table->foreign('RRNo', 'FK_RRConfirmationDetails_RRMaster')
                ->references('RRNo')->on('rrmaster')
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
