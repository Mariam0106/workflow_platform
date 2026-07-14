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
    Schema::create('validations', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Relationships
        $table->foreignId('request_id')
              ->constrained('requests')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('workflow_step_id')
              ->constrained('workflow_steps')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('validator_id')
              ->constrained('users')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Decision
        $table->enum('decision',[
            'PENDING',
            'APPROVED',
            'REJECTED',
            'RETURNED'
        ])->default('PENDING');

        $table->text('comment')->nullable();

        $table->timestamp('validated_at')->nullable();

        // Audit
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
