<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('purpose')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('days')->nullable();
            $table->string('status')->nullable();
            $table->string('destination_address')->nullable();
            $table->unsignedBigInteger('subordinate_staff_id')->nullable();
            $table->string('subordinate_staff_status')->nullable();
            $table->unsignedBigInteger('hod_id')->nullable();
            $table->string('hod_status')->nullable();
            $table->text('hod_comment')->nullable();
            $table->unsignedBigInteger('dean_id')->nullable();
            $table->string('dean_status')->nullable();
            $table->text('dean_comment')->nullable();
            $table->unsignedBigInteger('hr_id')->nullable();
            $table->string('hr_status')->nullable();
            $table->text('hr_comment')->nullable();
            $table->unsignedBigInteger('registrar_id')->nullable();
            $table->string('registrar_status')->nullable();
            $table->text('registrar_comment')->nullable();
            $table->date('registrar_approval_date')->nullable();
            $table->unsignedBigInteger('vc_id')->nullable();
            $table->string('vc_status')->nullable();
            $table->text('vc_comment')->nullable();
            $table->date('vc_approval_date')->nullable();
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
        Schema::dropIfExists('leaves');
    }
}
