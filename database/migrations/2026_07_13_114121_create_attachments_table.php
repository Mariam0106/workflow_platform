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
        $table->string('file_path');
        $table->string('mime_type',100);
        $table->unsignedBigInteger('file_size');

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
