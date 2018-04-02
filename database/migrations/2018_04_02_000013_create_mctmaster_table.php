<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMctmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'mctmaster';

    /**
     * Run the migrations.
     * @table mctmaster
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('MCTNo');
            $table->char('MIRSNo', 7);
            $table->date('MCTDate')->nullable()->default(null);
            $table->string('Particulars', 150)->nullable()->default(null);
            $table->string('AddressTo', 100)->nullable()->default(null);
            $table->char('Status', 1)->nullable()->default(null);
            $table->char('SignatureTurn', 1)->nullable()->default('0');
            $table->bigInteger('IsRollBack')->nullable()->default(null);
            $table->integer('CreatorID')->nullable()->default(null);
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
