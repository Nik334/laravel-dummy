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
        Schema::create('job_role_info', function (Blueprint $table) {
            $table->id();
            $table->string('document_path');
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id')->references('id')->on('designation');
            $table->integer('version');
            $table->enum('status', array('ACTIVE', 'INACTIVE'));
            $table->unsignedBigInteger('added_by');
            $table->foreign('added_by')->references('id')->on('users');
            $table->timestamp('added_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_role_info');
    }
};
