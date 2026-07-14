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
    Schema::create('workflows', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Category
        $table->foreignId('workflow_category_id')
              ->constrained('workflow_categories')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Business Information
        $table->string('code', 30)->unique();
        $table->string('name', 150);
        $table->text('description')->nullable();
        $table->string('version', 20)->default('1.0');

        // Configuration
        $table->boolean('is_default')->default(false);
        $table->boolean('is_active')->default(true);

        // Audit
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
