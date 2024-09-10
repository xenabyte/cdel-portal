<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Assuming there's a students table
            $table->string('job_title')->nullable();
            $table->text('job_requirements')->nullable(); // New field for job requirements
            $table->text('job_description')->nullable(); // New field for job description
            $table->foreignId('job_level_id')->nullable(); // Assuming there's a job_levels table
            $table->string('supervisor_name');
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
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
        Schema::dropIfExists('work_studies');
    }
}
