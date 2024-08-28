<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCareerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('career_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->onDelete('cascade');
            $table->text('biodata')->nullable();
            $table->text('education_history')->nullable(); 
            $table->text('professional_information')->nullable();
            $table->text('publications')->nullable(); 
            $table->string('cv_path')->nullable(); 
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
        Schema::dropIfExists('career_profiles');
    }
}
