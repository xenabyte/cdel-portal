<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCourseRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_course_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('file')->nullable();
            $table->string('level_adviser_status')->nullable();
            $table->string('hod_status')->nullable();
            $table->unsignedBigInteger('level_adviser_id')->nullable();
            $table->unsignedBigInteger('hod_id')->nullable();
            $table->date('level_adviser_approved_date')->nullable();
            $table->date('hod_approved_date')->nullable();
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
        Schema::dropIfExists('student_course_registrations');
    }
}
