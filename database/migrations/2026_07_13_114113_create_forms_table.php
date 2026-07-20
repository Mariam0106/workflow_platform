<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CORRECTION (Etape 0) : meme raisonnement que pour "workflows"
     * (version entiere, status Draft/Published/Archived, unicite
     * (code, version) au lieu de code seul) - BR-15/16/17.
     */
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Relationships
            $table->foreignId('form_category_id')
                  ->constrained('form_categories')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('workflow_id')
                  ->constrained('workflows')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Business Information
            $table->string('code', 30);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);

            // Lifecycle
            $table->string('status', 20)->default('Draft');
            $table->timestamp('published_at')->nullable();

            // Configuration
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Constraints
            $table->unique(['code', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
