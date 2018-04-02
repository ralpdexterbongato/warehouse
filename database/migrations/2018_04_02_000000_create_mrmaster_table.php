<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'mrmaster';

    /**
     * Run the migrations.
     * @table mrmaster
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('MRNo');
            $table->date('MRDate');
            $table->char('RVNo', 7);
            $table->date('RVDate');
            $table->char('RRNo', 7);
            $table->date('RRDate');
            $table->char('PONo', 7)->nullable()->default(null);
            $table->string('Note', 100)->nullable()->default(null);
            $table->string('Supplier', 50)->nullable()->default(null);
            $table->char('InvoiceNo', 12)->nullable()->default(null);
            $table->char('SignatureTurn', 1)->nullable()->default('0');
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
