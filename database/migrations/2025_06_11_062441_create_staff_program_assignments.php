<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffProgramAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_program_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('programme_category_id');
            $table->unsignedBigInteger('assigned_by_id');

            $table->string('slug')->nullable();
            
            $table->enum('role_in_programme', ['Secretary', 'Coordinator'])->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Suspended'])->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys with cascading deletes
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('programme_category_id')->references('id')->on('programme_categories')->onDelete('cascade');
            $table->foreign('assigned_by_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_program_assignments');
    }
}
