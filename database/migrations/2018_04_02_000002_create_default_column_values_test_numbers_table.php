<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultColumnValuesTestNumbersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'default_column_values_test_numbers';

    /**
     * Run the migrations.
     * @table default_column_values_test_numbers
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('int_column')->nullable()->default('1');
            $table->tinyInteger('tinyint_column')->nullable()->default('1');
            $table->smallInteger('smallint_column')->nullable()->default('1');
            $table->mediumInteger('mediumint_column')->nullable()->default('1');
            $table->bigInteger('bigint_column')->nullable()->default('1');
            $table->decimal('decimal_column')->nullable()->default('1.2');
            $table->double('double_column')->nullable()->default('1.2');
            $table->float('float_column')->nullable()->default('1.2');
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
