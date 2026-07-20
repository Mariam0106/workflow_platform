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
    Schema::create('form_categories', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Business Information
        $table->string('code', 30)->unique();
        $table->string('name', 100);
        $table->text('description')->nullable();

        // Display
        $table->string('icon')->nullable();
        $table->integer('display_order')->default(0);

        // Status
        $table->boolean('is_active')->default(true);

        // Audit
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_categories');
    }
};
