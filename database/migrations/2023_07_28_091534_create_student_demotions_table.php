<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentDemotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_demotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('old_level_id')->nullable();
            $table->unsignedBigInteger('new_level_id')->nullable();
            $table->unsignedBigInteger('old_programme_id')->nullable();
            $table->unsignedBigInteger('new_programme_id')->nullable();
            $table->string('reason')->nullable();
            $table->string('academic_session')->nullable();
            $table->boolean('is_approved')->default(false);
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
        Schema::dropIfExists('student_demotions');
    }
}
