<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing unique constraint on email field
            $table->dropUnique(['email']);
            $table->dropUnique(['phone_number']);

            // Add the composite unique constraint for email, phone number, and academic session
            $table->unique(['email', 'phone_number', 'academic_session']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['email', 'phone_number', 'academic_session']);

            // Restore the unique constraint on email field
            $table->unique('email');
        });
    }
}
