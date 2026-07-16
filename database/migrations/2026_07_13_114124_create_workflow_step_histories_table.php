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
     * Ajout de workflow_transition_id (nullable) : permet de savoir non
     * seulement PAR QUELLES ETAPES une Request est passee, mais aussi
     * QUELLE transition a cause chaque changement d'etape. Nullable
     * car la toute premiere etape est atteinte directement a la
     * soumission, pas via une transition.
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

            $table->foreignId('workflow_transition_id')
                  ->nullable()
                  ->constrained('workflow_transitions')
                  ->nullOnDelete()
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
