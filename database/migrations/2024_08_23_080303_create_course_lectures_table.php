<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lectures', function (Blueprint $table) {
            $table->id();
            $table->string('academic_session')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();    
            $table->string('topic')->nullable();
            $table->date('date')->nullable();
            $table->integer('duration')->nullable();
            $table->text('notes')->nullable();
            $table->string('video_link')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_lectures');
    }
}
