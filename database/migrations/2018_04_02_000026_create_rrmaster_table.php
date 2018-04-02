<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'RRMaster';

    /**
     * Run the migrations.
     * @table rrmaster
     *
     * @return void
     */
    public function up()
    {
      Schema::create('RRMaster', function (Blueprint $table) {
        $table->char('RRNo',7);
        $table->datetime('RRDate');
        $table->string('Supplier', 50)->nullable()->default(null);
        $table->string('Address', 100)->nullable()->default(null);
        $table->char('InvoiceNo', 12)->nullable()->default(null);
        $table->char('RVNo', 7)->nullable()->default(null);
        $table->string('Carrier', 30)->nullable()->default(null);
        $table->char('DeliveryReceiptNo', 12)->nullable()->default(null);
        $table->char('PONo', 7)->nullable()->default(null);
        $table->string('Note', 100)->nullable()->default(null);
        $table->char('Status', 1)->nullable()->default(null);
        $table->bigInteger('IsRollBack')->nullable()->default(null);
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
