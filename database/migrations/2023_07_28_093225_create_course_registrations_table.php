<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('course_credit_unit');
            $table->unsignedBigInteger('semester');
            $table->string('course_code')->nullable();
            $table->integer('ca_score')->nullable();
            $table->integer('exam_score')->nullable();
            $table->integer('total')->nullable();
            $table->string('grade')->nullable();
            $table->integer('points')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('level')->nullable();
            $table->unsignedBigInteger('result_approval_id')->nullable();
            $table->string('status')->default('Pending');
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
        Schema::dropIfExists('course_registrations');
    }
}
