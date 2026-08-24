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
    Schema::create('application_roles', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Business Information
        $table->string('code', 30)->unique();
        $table->string('name', 100);
        $table->text('description')->nullable();

        // Permissions
        $table->boolean('is_system')->default(false);
        $table->boolean('is_active')->default(true);

        // Audit
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_roles');
    }
};
