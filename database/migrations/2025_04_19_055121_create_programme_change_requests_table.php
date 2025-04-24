<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammeChangeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programme_change_requests', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->foreignId('student_id')->constrained();
            $table->foreignId('old_programme_id')->constrained('programmes');
            $table->foreignId('new_programme_id')->constrained('programmes');
            $table->text('reason')->nullable();

            // Status and Stage
            $table->string('status')->default('submitted'); // e.g. submitted, in_review, approved, rejected
            $table->string('current_stage')->default('HOD_OLD_APPROVAL');

            // Payment
            $table->integer('transaction_id')->nullable();

            // Approver IDs
            $table->foreignId('old_programme_hod_id')->nullable();
            $table->foreignId('old_programme_dean_id')->nullable();
            $table->foreignId('new_programme_hod_id')->nullable();
            $table->foreignId('new_programme_dean_id')->nullable();
            $table->foreignId('dap_id')->nullable(); // Dean Academic Planning
            $table->foreignId('registrar_id')->nullable();

            // Approval Timestamps
            $table->timestamp('hod_old_approved_at')->nullable();
            $table->timestamp('dean_old_approved_at')->nullable();
            $table->timestamp('hod_new_approved_at')->nullable();
            $table->timestamp('dean_new_approved_at')->nullable();
            $table->timestamp('dap_approved_at')->nullable();
            $table->timestamp('registrar_approved_at')->nullable();

            // Optional: Rejection Reason
            $table->text('rejection_reason')->nullable();

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
        Schema::dropIfExists('programme_change_requests');
    }
}
