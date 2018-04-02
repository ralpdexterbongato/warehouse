<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('FullName', 40);
            $table->smallInteger('Role');
            $table->string('Position', 30);
            $table->string('Username', 30);
            $table->string('password', 191);
            $table->string('Signature', 100);
            $table->smallInteger('IfApproveReplacer')->nullable()->default(null);
            $table->char('IsActive', 1)->nullable()->default('0');
            $table->bigInteger('Manager')->nullable()->default(null);
            $table->rememberToken();
            $table->char('Mobile', 11)->nullable()->default(null);
            $table->dateTime('LastOnline')->nullable()->default(null);
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
