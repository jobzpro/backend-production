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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("product_id")->nullable();
            $table->integer("product_plan_id")->nullable();
            $table->integer("connection_count")->default(20)->nullable();
            $table->integer("post_count")->default(10)->nullable();
            $table->integer("applicant_count")->default(20)->nullable();
            $table->dateTime("expiry_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
