<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicSessionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_session_settings', function (Blueprint $table) {
            $table->id();
            $table->string('academic_session')->nullable();
            $table->string('admission_session')->nullable();
            $table->string('application_session')->nullable();
            $table->string('resumption_date')->nullable();
            $table->string('programme_category_id')->nullable();
            $table->string('school_fee_status')->nullable();
            $table->string('accomondation_booking_status')->nullable();
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
        Schema::dropIfExists('academic_session_settings');
    }
}
