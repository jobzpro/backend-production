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
        Schema::table('job_lists', function(Blueprint $table){
            $table->string('show_pay')->after('description');
            $table->string('min_salary')->after('salary')->nullable();
            $table->string('max_salary')->after('min_salary')->nullable();
            $table->string('pay_type')->nullable();
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
