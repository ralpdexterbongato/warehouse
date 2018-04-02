<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasteritemsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'masteritems';

    /**
     * Run the migrations.
     * @table masteritems
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('ItemCode',20);
            $table->string('AccountCode', 20);
            $table->string('Description', 100)->nullable()->default(null);
            $table->string('Unit', 20)->nullable()->default(null);
            $table->bigInteger('CurrentQuantity');
            $table->bigInteger('AlertIfBelow')->nullable()->default(null);
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
