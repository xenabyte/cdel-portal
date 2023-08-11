<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('award')->nullable();
            $table->string('duration')->nullable();
            $table->string('max_duration')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->integer('code_number')->nullable();
            $table->string('code')->nullable();
            $table->string('matric_last_number')->nullable();
            $table->unsignedBigInteger('web_id')->unique();
            $table->string('slug')->nullable();
            $table->string('academic_session')->nullable();
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
        Schema::dropIfExists('programmes');
    }
}
