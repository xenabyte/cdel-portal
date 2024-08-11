<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->integer('amount_payed')->nullable();
            $table->string('reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->string('session')->nullable();
            $table->string('is_used')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('checkout_url')->nullable();
            $table->string('plan_id')->nullable();
            $table->text('additional_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
