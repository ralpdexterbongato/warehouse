<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCanvassdetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'canvassdetails';

    /**
     * Run the migrations.
     * @table canvassdetails
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
            $table->decimal('Price', 18, 2)->nullable()->default(null);
            $table->string('Unit', 10);
            $table->string('Article', 100);
            $table->bigInteger('Qty');
            $table->integer('CanvassMasters_id');

            $table->index(["CanvassMasters_id"], 'FK_CanvassDetails_CanvassMasters');


            $table->foreign('CanvassMasters_id', 'FK_CanvassDetails_CanvassMasters')
                ->references('id')->on('canvassmasters')
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
