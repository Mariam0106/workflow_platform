<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CORRECTION (Etape 0) :
     * - "version" devient un entier (1, 2, 3...) au lieu d'une chaine
     *   libre "1.0" : necessaire pour trier/incrementer les versions
     *   d'un meme workflow (BR-24).
     * - Ajout de "status" (Draft / Published / Archived) : un simple
     *   is_active ne permet pas de distinguer "jamais publie" de
     *   "publie puis archive", alors que BR-15/16/26 exigent ce
     *   distinguo. Stocke en varchar (pas un ENUM MySQL natif) pour
     *   pouvoir faire evoluer les valeurs sans nouvelle migration
     *   (BR-56/57) - le cast PHP (Enum) sera ajoute a l'Etape 1.
     * - "code" n'est plus globalement unique : plusieurs versions du
     *   MEME workflow doivent pouvoir partager le meme code. La
     *   combinaison (code, version) est unique a la place.
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
            $table->string('code', 30);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);

            // Lifecycle
            $table->string('status', 20)->default('Draft');
            $table->timestamp('published_at')->nullable();

            // Configuration
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('workflows');
    }
};
