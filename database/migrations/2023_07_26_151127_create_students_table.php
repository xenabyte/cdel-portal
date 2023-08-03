<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('matric_number')->nullable();
            $table->static('slug')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('passcode')->nullable();
            $table->unsignedBigInteger('programme_id')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->integer('credit_load')->default(0);
            $table->boolean('is_passed_out')->default(false);
            $table->boolean('is_rusticated')->default(false);
            $table->boolean('is_active')->default(false);
            $table->integer('amount_balance')->default(0);
            $table->year('entry_year')->nullable();
            $table->year('max_graduating_year')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->string('admission_letter')->nullable();
            $table->string('faculty_id')->nullable();
            $table->string('department_id')->nullable();
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
        Schema::drop('students');
    }
}
