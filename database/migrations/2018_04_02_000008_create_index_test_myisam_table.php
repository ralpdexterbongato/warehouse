<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexTestMyisamTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'index_test_myisam';

    /**
     * Run the migrations.
     * @table index_test_myisam
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('unique_column')->nullable();
            $table->integer('index_column')->nullable();
            $table->integer('combined_index_column_1')->nullable();
            $table->integer('combined_index_column_2')->nullable();

            $table->index(["index_column"], 'index_column_INDEX');

            $table->index(["combined_index_column_1", "combined_index_column_2"], 'combined_index_column_INDEX');

            $table->unique(["unique_column"], 'unique_column_UNIQUE');
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
