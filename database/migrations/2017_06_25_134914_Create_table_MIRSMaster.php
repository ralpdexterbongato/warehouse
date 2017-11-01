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
            $table->string('PreparedPosition',50)->nullable();
            $table->string('RecommendPosition',50)->nullable();
            $table->string('PreparedSignature',140)->nullable();
            $table->string('RecommendSignature',140)->nullable();
            $table->string('ApproveSignature',140)->nullable();
            $table->char('WithMCT',1)->nullable();
            $table->string('IfDeclined',50)->nullable();
            $table->string('ManagerReplacer',50)->nullable();
            $table->string('ManagerReplacerSignature',150)->nullable();
            $table->string('ApprovalReplacer',50)->nullable();
            $table->string('ApprovalReplacerSignature',150)->nullable();
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
