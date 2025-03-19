<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSuspensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_suspensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file')->nullable();
            $table->string('slug')->nullable();
            $table->string('status')->default('pending');
            $table->string('academic_session')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('court_affidavit')->nullable();
            $table->string('undertaking_letter')->nullable();
            $table->string('traditional_ruler_reference')->nullable();
            $table->string('ps_reference')->nullable();
            $table->text('admin_comment')->nullable();
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
        Schema::dropIfExists('student_suspensions');
    }
}
