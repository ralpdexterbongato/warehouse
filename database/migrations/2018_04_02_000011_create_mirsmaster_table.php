<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMirsmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'mirsmaster';

    /**
     * Run the migrations.
     * @table mirsmaster
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('MIRSNo');
            $table->string('Purpose', 100)->nullable()->default(null);
            $table->date('MIRSDate')->nullable()->default(null);
            $table->char('WithMCT', 1)->nullable()->default(null);
            $table->char('Status', 1)->nullable()->default(null);
            $table->char('SignatureTurn', 1)->nullable()->default('0');
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
