<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelAdvisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_advisers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programme_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('course_approval_status')->nullable();
            $table->text('comment')->nullable();
            $table->string('course_registration')->nullable();
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
        Schema::dropIfExists('level_advisers');
    }
}
