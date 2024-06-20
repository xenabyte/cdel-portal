<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->string('lastname')->nullable();
            $table->string('othernames')->nullable();
            $table->string('staffId')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('signature')->nullable();
            $table->string('qualification')->nullable();
            $table->string('department')->nullable();
            $table->string('current_position')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->nullable();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->string('change_password')->nullable();
            $table->string('unit_id')->nullable();
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
        Schema::drop('staff');
    }
}
