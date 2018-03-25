<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->index();
            $table->string('payer')->nullable()->index();
            $table->string('payee')->index();
            $table->integer('amount');
            $table->integer('native')->nullable();
            $table->integer('default')->nullable();
            $table->smallInteger('type');
            $table->smallInteger('status')->index();
            $table->char('currency', 3);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['payer', 'payee', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
