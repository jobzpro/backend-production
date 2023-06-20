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
            $table->string('phone_number')->after('last_name')->nullable();
            $table->string('address_line')->after('avatar_path')->nullable();
            $table->string('city')->after('address_line')->nullable();
            $table->string('province')->after('city')->nullable();
            $table->string('elementary_school')->after('province')->nullable();
            $table->string('high_school')->after('elementary_school')->nullable();
            $table->string('college')->after('high_school')->nullable();
            $table->text('description')->after('college')->nullable();
            $table->text('certifications')->after('description')->nullable();
            $table->text('skills')->after('certifications')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::dropIfExists('users');
    }
};
