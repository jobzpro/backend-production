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
        Schema::create('job_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_list_id');
            $table->string('standard_shift')->nullable();
            $table->string('weekly_schedule')->nullable();
            $table->string('supplemental_schedule')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_shifts');
    }
};
