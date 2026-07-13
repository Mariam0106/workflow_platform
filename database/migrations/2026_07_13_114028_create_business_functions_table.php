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
    Schema::create('business_functions', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Parent Department
        $table->foreignId('department_id')
              ->constrained('departments')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Business Information
        $table->string('code', 20);
        $table->string('name', 150);
        $table->text('description')->nullable();

        // Status
        $table->boolean('is_active')->default(true);

        // Audit
        $table->timestamps();
        $table->softDeletes();

        // Constraints
        $table->unique(['department_id', 'code']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_functions');
    }
};
