<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysdiagramsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'sysdiagrams';

    /**
     * Run the migrations.
     * @table sysdiagrams
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('name', 160);
            $table->integer('principal_id');
            $table->increments('diagram_id');
            $table->integer('version')->nullable()->default(null);
            $table->binary('definition')->nullable()->default(null);

            $table->unique(["principal_id", "name"], 'UK_principal_name');
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
