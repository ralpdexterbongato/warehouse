<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePomastersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'POMasters';

    /**
     * Run the migrations.
     * @table pomasters
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('PONo',7);
            $table->char('RVNo', 7);
            $table->string('Supplier', 50);
            $table->string('Address', 150)->nullable()->default(null);
            $table->char('Telephone', 15)->nullable()->default(null);
            $table->string('Purpose', 100)->nullable()->default(null);
            $table->date('RVDate')->nullable()->default(null);
            $table->date('PODate')->nullable()->default(null);
            $table->char('Status', 1)->nullable()->default(null);
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
