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
    Schema::create('transition_conditions', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Parent Transition
        $table->foreignId('workflow_transition_id')
              ->constrained('workflow_transitions')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        // Business Rule
        $table->string('field_name',100);
        $table->string('operator',30);
        $table->string('expected_value',255)->nullable();

        // Logic
        $table->integer('execution_order')->default(1);

        // Audit
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transition_conditions');
    }
};
