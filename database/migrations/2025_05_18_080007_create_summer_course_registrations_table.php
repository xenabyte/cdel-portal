<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummerCourseRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summer_course_registrations', function (Blueprint $table) {
            $table->id();
            // Basic Info
            $table->integer('student_id')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('course_registration_id')->nullable();
            $table->string('course_id')->nullable();
            $table->integer('programme_category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summer_course_registrations');
    }
}
