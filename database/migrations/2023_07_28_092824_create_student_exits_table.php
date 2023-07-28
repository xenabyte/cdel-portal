<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('exit_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamp('exited_at')->nullable();
            $table->timestamp('return_at')->nullable();
            $table->string('status')->default('Pending');
            $table->boolean('is_dap_approved')->default(false);
            $table->timestamp('is_dap_approved_date')->nullable();
            $table->boolean('is_registrar_approved')->default(false);
            $table->timestamp('is_registrar_approved_date')->nullable();
            $table->boolean('is_guardian_approved')->default(false);
            $table->timestamp('is_guardian_approved_date')->nullable();
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
        Schema::dropIfExists('student_exits');
    }
}
