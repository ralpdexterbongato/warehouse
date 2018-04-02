<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultColumnValuesTestStringsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'default_column_values_test_strings';

    /**
     * Run the migrations.
     * @table default_column_values_test_strings
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('varchar_column', 45)->nullable()->default('test');
            $table->text('text_column')->nullable()->default('test');
            $table->mediumText('mediumtext_column')->nullable()->default('test');
            $table->longText('longtext_column')->nullable()->default('test');
            $table->text('tinytext_column')->nullable()->default('test');
            $table->char('char_column', 3)->nullable()->default('test');
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
