<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMirsmasterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */

    /**
     * Run the migrations.
     * @table mirsmaster
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MIRSMaster', function (Blueprint $table) {
          $table->char('MIRSNo',7);
          $table->string('Purpose', 100)->nullable()->default(null);
          $table->datetime('mirsdate')->nullable()->default(null);
          $table->char('WithMCT', 1)->nullable()->default(null);
          $table->char('Status', 1)->nullable()->default(null);
          $table->char('SignatureTurn', 1)->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('MIRSMaster');
     }
}
