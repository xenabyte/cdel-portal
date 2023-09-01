<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePerProgrammePerAcademicSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_per_programme_per_academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programme_id')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('credit_unit');
            $table->integer('semester')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->string('status');
            $table->string('academic_session')->nullable();
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
        Schema::dropIfExists('course_per_programme_per_academic_sessions');
    }
}
