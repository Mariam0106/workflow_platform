<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CORRECTION (Etape 4) : cette migration ne correspondait pas au
     * Model Attachment, qui attend deja storage_path / size / extension
     * (jamais detecte plus tot car aucun test de bout en bout n'avait
     * encore cree d'Attachment).
     * - file_path -> storage_path
     * - file_size -> size
     * - ajout de "extension" (utile pour l'affichage d'icone/filtrage
     *   sans reparser mime_type a chaque fois)
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Parent Request
            $table->foreignId('request_id')
                  ->constrained('requests')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // File Information
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('storage_path');
            $table->string('mime_type', 100);
            $table->string('extension', 20);
            $table->unsignedBigInteger('size');

            // Uploaded By
            $table->foreignId('uploaded_by')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Audit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
