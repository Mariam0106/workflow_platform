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
     * Ajout de manager_id (auto-reference nullable) pour le responsable
     * hierarchique N+1, exige explicitement par le cahier des charges
     * ("Les responsables hierarchiques (N+1)") et absent de la version
     * precedente.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Organization
            $table->foreignId('entity_id')
                  ->constrained('entities')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('business_function_id')
                  ->constrained('business_functions')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('application_role_id')
                  ->constrained('application_roles')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Hierarchical manager (N+1) - nullable: top of hierarchy has none
            $table->foreignId('manager_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            // Identity
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();

            // Authentication
            $table->string('password');
            $table->rememberToken();

            // Employee Information
            $table->string('employee_number', 30)->unique()->nullable();
            $table->string('job_title', 150)->nullable();

            // Status
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
