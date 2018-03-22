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
	        $table->string('name');
	        $table->char('country', 2);
	        $table->string('city', 200);
            $table->char('purse', 24)->unique()->nullable();
	        $table->char('currency', 3);
	        $table->integer('amount');
	        $table->dateTime('created_at');
	        $table->dateTime('updated_at');
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
