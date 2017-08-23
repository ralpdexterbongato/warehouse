<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMRMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MRMaster', function (Blueprint $table) {
            $table->char('MRNo',7)->primary();
            $table->date('MRDate');
            $table->char('RVNo',7);
            $table->date('RVDate');
            $table->char('RRNo',7);
            $table->date('RRDate');
            $table->char('PONo',7)->nullable();
            $table->string('Note',100)->nullable();
            $table->string('Supplier',50)->nullable();
            $table->char('InvoiceNo',12);
            $table->string('Recommendedby',50)->nullable();
            $table->string('RecommendedbyPosition',50)->nullable();
            $table->string('RecommendedbySignature',150)->nullable();
            $table->string('GeneralManager',50)->nullable();
            $table->string('GeneralManagerSignature',150)->nullable();
            $table->string('Receivedby',50)->nullable();
            $table->string('ReceivedbyPosition',50)->nullable();
            $table->string('WarehouseMan',50)->nullable();
            $table->string('IfDeclined',50)->nullable();
            $table->string('ApprovalReplacerFname',30)->nullable();
            $table->string('ApprovalReplacerLname',30)->nullable();
            $table->string('ApprovalReplacerPosition',50)->nullable();
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
        Schema::dropIfExists('MRMaster');
    }
}
