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
    Schema::create('workflow_step_histories', function (Blueprint $table) {

        $table->id();

        $table->foreignId('request_id')
              ->constrained('requests')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('workflow_step_id')
              ->constrained('workflow_steps')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        $table->timestamp('entered_at');
        $table->timestamp('left_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_step_histories');
    }
};
