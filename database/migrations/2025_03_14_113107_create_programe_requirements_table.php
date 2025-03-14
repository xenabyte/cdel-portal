<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrameRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programme_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('programme_id')->nullable(); 
            $table->integer('level_id')->nullable(); 
            $table->decimal('min_cgpa', 3, 2)->nullable(); 
            $table->text('additional_criteria')->nullable(); 
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
        Schema::dropIfExists('programme_requirements');
    }
}
