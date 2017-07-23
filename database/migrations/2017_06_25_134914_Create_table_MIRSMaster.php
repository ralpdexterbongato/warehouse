<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMIRSMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MIRSMaster', function (Blueprint $table) {
            $table->increments('id');
            $table->char('MIRSNo',9);
            $table->string('Purpose',100)->nullable();
            $table->string('Preparedby',50)->nullable();
            $table->string('Recommendedby',50)->nullable();
            $table->string('Approvedby',50)->nullable();
            $table->date('MIRSDate')->nullable();
            $table->string('PreparedPosition')->nullable();
            $table->string('RecommendPosition')->nullable();
            $table->string('ApprovePosition')->nullable();
            $table->string('PreparedSignature',140)->nullable();
            $table->string('RecommendSignature',140)->nullable();
            $table->string('ApproveSignature',140)->nullable();
            $table->char('WithMCT',1)->nullable();
            $table->string('IfDenied',50)->nullable();

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
