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
    Schema::create('request_values', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Relationships
        $table->foreignId('request_id')
              ->constrained('requests')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('form_field_id')
              ->constrained('form_fields')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Stored Value
        $table->longText('value')->nullable();

        // Audit
        $table->timestamps();

        // Constraints
        $table->unique(['request_id','form_field_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_values');
    }
};
