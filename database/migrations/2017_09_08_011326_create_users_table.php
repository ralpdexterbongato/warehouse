<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('FullName',40);
            $table->decimal('Role',2,0);
            $table->string('Position',30);
            $table->string('Username',30);
            $table->string('password',191);
            $table->string('Signature',100);
            $table->decimal('IfApproveReplacer',1,0)->nullable();
            $table->char('IsActive',1)->default('0')->nullable();
            $table->decimal('Manager',11,0)->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
