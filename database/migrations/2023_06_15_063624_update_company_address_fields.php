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
            $table->renameColumn('email', 'company_email');
            $table->renameColumn('address', 'address_line');
            $table->string('city')->after('address');
            $table->string('state')->after('city');
            $table->integer('zip_code')->after('state')->nullable();
            $table->integer('business_type_id')->nullable();
            $table->string('owner_full_name');
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
