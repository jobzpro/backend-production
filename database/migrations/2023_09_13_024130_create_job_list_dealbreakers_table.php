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
        Schema::create('job_list_dealbreakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_list_id');
            $table->foreignId('dealbreaker_id');
            $table->boolean('required');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_list_dealbreakers');
    }
};
