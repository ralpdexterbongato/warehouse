<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRVMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RVMasters', function (Blueprint $table) {
            $table->increments('id');
            $table->char('RVNo',7);
            $table->date('RVDate')->nullable();
            $table->string('Purpose',100)->nullable();
            $table->string('Requisitioner',50)->nullable();
            $table->string('RequisitionerPosition',50)->nullable();
            $table->string('RequisitionerSignature',150)->nullable();
            $table->string('Recommendedby',50)->nullable();
            $table->string('RecommendedbyPosition',50)->nullable();
            $table->string('RecommendedbySignature',150)->nullable();
            $table->string('BudgetOfficer',50)->nullable();
            $table->string('BudgetOfficerSignature',150)->nullable();
            $table->string('GeneralManager',50)->nullable();
            $table->string('GeneralManagerSignature',150)->nullable();
            $table->string('BudgetAvailable',50)->nullable();
            $table->string('IfDeclined',50)->nullable();
            $table->char('IfPurchased',4)->nullable();
            $table->string('ManagerReplacer',50)->nullable();
            $table->string('ManagerReplacerSignature',150)->nullable();
            $table->string('ApprovalReplacer',50)->nullable();
            $table->string('ApprovalReplacerSignature',150)->nullable();
            $table->string('PendingRemarks',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RVMasters');
    }
}
