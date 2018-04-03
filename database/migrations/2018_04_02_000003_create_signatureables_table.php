<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignatureablesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */

    /**
     * Run the migrations.
     * @table signatureables
     *
     * @return void
     */
    public function up()
    {
      Schema::create('Signatureables', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        $table->char('Signatureable_id', 7);
        $table->string('Signatureable_type', 50);
        $table->string('Signature', 140)->nullable()->default(null);
        $table->string('SignatureType', 50)->nullable()->default(null);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('Signatureables');
     }
}
