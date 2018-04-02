<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultColumnValuesTestTimeTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'default_column_values_test_time';

    /**
     * Run the migrations.
     * @table default_column_values_test_time
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->time('time_column_zero')->nullable()->default('0');
            $table->time('time_column_null')->nullable()->default(null);
            $table->date('date_column_zero')->nullable()->default('0');
            $table->date('date_column_null')->nullable()->default(null);
            $table->timestamp('timestamp_column_null')->nullable()->default(null);
            $table->timestamp('timestamp_column_zero')->nullable()->default('0');
            $table->timestamp('timestamp_column_current_timestamp')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('timestamp_column_null_on_update_current_timestamp')->nullable()->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('timestamp_column_current_timestamp_on_update_current_timestamp')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->dateTime('datetime_column_null')->nullable()->default(null);
            $table->dateTime('datetime_column_zero')->nullable()->default('0');
            $table->dateTime('datetime_column_current_timestamp')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('datetime_column_null_on_update_current_timestamp')->nullable()->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'));
            $table->dateTime('datetime_column_null_current_timestamp_on_update_current_timestamp')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->smallInteger('year_column_null')->nullable()->default('0');
            $table->smallInteger('year_column_zero')->nullable()->default('0');
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
