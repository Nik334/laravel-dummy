<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_name');
            $table->string('mobile_number');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('role');
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id')->references('id')->on('designation');
            $table->unsignedBigInteger('added_by');
            $table->foreign('added_by')->references('id')->on('users');
            $table->timestamp('added_on')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['role_id']);
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['added_by']);

            // Drop columns
            $table->dropColumn('role_id');
            $table->dropColumn('designation_id');
            $table->dropColumn('added_by');
        });
    }
};
