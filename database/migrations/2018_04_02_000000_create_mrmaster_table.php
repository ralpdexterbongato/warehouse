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

    /**
     * Run the migrations.
     * @table mrmaster
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MRMaster', function (Blueprint $table) {
        $table->char('MRNo',7);
        $table->datetime('MRDate');
        $table->char('RVNo', 7);
        $table->datetime('RVDate');
        $table->char('RRNo', 7);
        $table->datetime('RRDate');
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
       Schema::dropIfExists('MRMaster');
     }
}
