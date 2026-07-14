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
    Schema::create('workflow_steps', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Parent Workflow
        $table->foreignId('workflow_id')
              ->constrained('workflows')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Business Information
        $table->string('code',30);
        $table->string('name',150);
        $table->text('description')->nullable();

        // Step Configuration
        $table->integer('step_order');
        $table->boolean('is_initial')->default(false);
        $table->boolean('is_final')->default(false);

        // Validator Configuration
        $table->enum('validator_type',[
            'ROLE',
            'USER',
            'N_PLUS_1',
            'ENTITY_MANAGER',
            'DEPARTMENT_MANAGER'
        ]);

        $table->unsignedBigInteger('validator_reference')->nullable();

        // Status
        $table->boolean('is_active')->default(true);

        // Audit
        $table->timestamps();

        // Constraints
        $table->unique(['workflow_id','step_order']);
        $table->unique(['workflow_id','code']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
