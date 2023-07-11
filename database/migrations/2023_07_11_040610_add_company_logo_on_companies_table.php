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
        Schema::table('companies', function(Blueprint $table){
            $table->string('company_logo_path')->after('id')->nullable();
            $table->string('owner_contact_no')->after('owner_full_name')->nullable();
            $table->string('years_of_operation')->nullable();
            $table->string('referral_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
