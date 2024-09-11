<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        {
			// Schema::table('job_lists', function(Blueprint $table){
			//     $table->boolean('is_vaccinated')->change();
			//     $table->boolean('can_commute')->change();
			// });
			DB::statement('ALTER TABLE job_lists ALTER COLUMN 
					  is_vaccinated TYPE boolean USING (is_vaccinated)::boolean');
			DB::statement('ALTER TABLE job_lists ALTER COLUMN 
					  can_commute TYPE boolean USING (can_commute)::boolean');
		}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_lists');
    }
};
