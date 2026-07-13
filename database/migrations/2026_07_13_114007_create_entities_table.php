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
    Schema::create('entities', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Business Information
        $table->string('code', 20)->unique();
        $table->string('name', 150);
        $table->string('legal_name', 255)->nullable();
        $table->string('email')->nullable();
        $table->string('phone', 30)->nullable();
        $table->string('website')->nullable();

        // Address
        $table->string('address')->nullable();
        $table->string('city', 100)->nullable();
        $table->string('country', 100)->default('Morocco');

        // Status
        $table->boolean('is_active')->default(true);

        // Laravel timestamps
        $table->timestamps();

        // Soft delete (optional but recommended)
        $table->softDeletes();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
