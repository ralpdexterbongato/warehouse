<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRRMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RRMaster', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('RRNo',7);
            $table->date('RRDate');
            $table->string('Supplier',50)->nullable();
            $table->string('Address',100)->nullable();
            $table->char('InvoiceNo',12)->nullable();
            $table->char('RVNo',7)->nullable();
            $table->string('Carrier',30)->nullable();
            $table->char('DeliveryReceiptNo',12)->nullable();
            $table->char('PONo',7)->nullable();
            $table->string('Note',100)->nullable();
            $table->string('Receivedby',50)->nullable();
            $table->string('ReceivedbySignature',150)->nullable();
            $table->string('ReceivedbyPosition',50)->nullable();
            $table->string('Verifiedby',50)->nullable();
            $table->string('VerifiedbySignature',150)->nullable();
            $table->string('VerifiedbyPosition',50)->nullable();
            $table->string('ReceivedOriginalby',50)->nullable();
            $table->string('ReceivedOriginalbySignature',150)->nullable();
            $table->string('ReceivedOriginalbyPosition',50)->nullable();
            $table->string('PostedtoBINby',50)->nullable();
            $table->string('PostedtoBINbySignature',150)->nullable();
            $table->string('PostedtoBINbyPosition',50)->nullable();
            $table->string('IfDeclined',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RRMaster');
    }
}
