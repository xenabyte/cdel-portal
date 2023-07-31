<?php

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
            $table->bigIncrements('id');
            $table->string('slug')->nullable();
            $table->string('application_number')->nullable();
            $table->string('passcode')->nullable();
            $table->string('password')->nullable();
            $table->string('lastname')->nullable();
            $table->string('othernames')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('programme_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('sitting_no')->nullable();
            $table->string('olevel_1')->nullable();
            $table->string('olevel_2')->nullable();
            $table->text('schools_attended')->nullable();
            $table->string('status')->nullable();
            $table->string('academic_session')->nullable();
            $table->unsignedBigInteger('guardian_id')->nullable();
            $table->unsignedBigInteger('next_of_kin_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
