<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrtmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'mrtmaster';

    /**
     * Run the migrations.
     * @table mrtmaster
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('MRTNo',7);
            $table->char('MCTNo', 7);
            $table->date('ReturnDate')->nullable()->default(null);
            $table->string('Particulars', 100)->nullable()->default(null);
            $table->string('AddressTo', 50)->nullable()->default(null);
            $table->string('Remarks', 50)->nullable()->default(null);
            $table->char('SignatureTurn', 1)->nullable()->default('0');
            $table->char('Status', 1)->nullable()->default(null);
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
