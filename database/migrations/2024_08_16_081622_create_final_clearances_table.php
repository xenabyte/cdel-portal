<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalClearancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_clearances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->text('experience')->nullable();
            $table->unsignedBigInteger('hod_id')->nullable();
            $table->string('hod_status')->nullable();
            $table->text('hod_comment')->nullable();
            $table->dateTime('hod_approval_date')->nullable();
            $table->unsignedBigInteger('dean_id')->nullable();
            $table->string('dean_status')->nullable();
            $table->text('dean_comment')->nullable();
            $table->dateTime('dean_approval_date')->nullable();
            $table->unsignedBigInteger('student_care_dean_id')->nullable();
            $table->string('student_care_dean_status')->nullable();
            $table->text('student_care_dean_comment')->nullable();
            $table->dateTime('student_care_dean_approval_date')->nullable();
            $table->unsignedBigInteger('registrar_id')->nullable();
            $table->string('registrar_status')->nullable();
            $table->text('registrar_comment')->nullable();
            $table->dateTime('registrar_approval_date')->nullable();
            $table->unsignedBigInteger('bursary_id')->nullable();
            $table->string('bursary_status')->nullable();
            $table->text('bursary_comment')->nullable();
            $table->dateTime('bursary_approval_date')->nullable();
            $table->unsignedBigInteger('library_id')->nullable();
            $table->string('library_status')->nullable();
            $table->text('library_comment')->nullable();
            $table->dateTime('library_approval_date')->nullable();
            $table->string('status')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys and indexes
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('hod_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dean_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('student_care_dean_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('registrar_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('bursary_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('library_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('final_clearances');
    }
}
