<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'jobs';

    /**
     * Run the migrations.
     * @table jobs
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('queue', 191);
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->integer('reserved_at')->nullable()->default(null);
            $table->integer('available_at');
            $table->integer('created_at');

            $table->index(["queue", "reserved_at"], 'jobs_queue_reserved_at_index');
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
