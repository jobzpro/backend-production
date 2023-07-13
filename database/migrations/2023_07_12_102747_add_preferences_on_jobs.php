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
        Schema::table('job_lists',function(Blueprint $table){
            $table->string('require_resume')->nullable();
            $table->boolean('can_applicant_with_criminal_record_apply')->nullable();
            $table->boolean('can_start_messages')->nullable();
            $table->boolean('send_auto_reject_emails')->nullable();
            $table->boolean('auto_reject')->nullable();
            $table->dateTime('time_limit')->nullable();
            $table->string('other_email')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_lists');
    }
};
