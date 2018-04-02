<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvmastersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'rvmasters';

    /**
     * Run the migrations.
     * @table rvmasters
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('RVNo');
            $table->date('RVDate')->nullable()->default(null);
            $table->string('Purpose', 100)->nullable()->default(null);
            $table->string('BudgetAvailable', 50)->nullable()->default(null);
            $table->char('IfPurchased', 4)->nullable()->default(null);
            $table->string('PendingRemarks', 100)->nullable()->default(null);
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
       Schema::dropIfExists($this->set_schema_table);
     }
}
