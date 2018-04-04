<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMctmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */

    /**
     * Run the migrations.
     * @table mctmaster
     *
     * @return void
     */
    public function up()
    {
      Schema::create('MCTMaster', function (Blueprint $table) {
        $table->char('MCTNo',7);
        $table->char('MIRSNo', 7);
        $table->datetime('mctdate')->nullable()->default(null);
        $table->string('Particulars', 150)->nullable()->default(null);
        $table->string('AddressTo', 100)->nullable()->default(null);
        $table->char('Status', 1)->nullable()->default(null);
        $table->char('SignatureTurn', 1)->nullable()->default('0');
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
       Schema::dropIfExists('MCTMaster');
     }
}
