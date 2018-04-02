<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnsTestFlagsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'columns_test_flags';

    /**
     * Run the migrations.
     * @table columns_test_flags
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->time('time_column')->nullable();
            $table->date('date_column')->nullable();
            $table->timestamp('timestamp_column')->nullable();
            $table->dateTime('datetime_column')->nullable();
            $table->smallInteger('year_column')->nullable();
            $table->enum('enum_column', ['test', 'test1'])->nullable();
            $table->unsignedInteger('int_column_zerofill_unsigned')->nullable();
            $table->integer('int_column_zerofill')->nullable();
            $table->unsignedInteger('int_column')->nullable();
            $table->unsignedTinyInteger('tinyint_column_zerofil_unsigned')->nullable();
            $table->tinyInteger('tinyint_column_zerofil')->nullable();
            $table->unsignedTinyInteger('tinyint_column')->nullable();
            $table->unsignedSmallInteger('smallint_column')->nullable();
            $table->unsignedMediumInteger('mediumint_column')->nullable();
            $table->unsignedBigInteger('bigint_column')->nullable();
            $table->decimal('decimal_column')->nullable();
            $table->double('double_column')->nullable();
            $table->float('float_column')->nullable();
            $table->string('varchar_column', 45)->nullable();
            $table->text('text_column')->nullable();
            $table->mediumText('mediumtext_column')->nullable();
            $table->longText('longtext_column');
            $table->text('tinytext_column');
            $table->char('char_column', 3);
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
