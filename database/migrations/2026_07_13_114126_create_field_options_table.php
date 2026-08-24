<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * AJOUT : remplace la colonne form_fields.options (JSON) pour les
     * champs Select/Radio/Checkbox/MultiSelect. Une table dediee permet
     * a un administrateur de desactiver une option, changer son ordre,
     * ou en ajouter une nouvelle sans toucher au code (BR-56/57) - ce
     * qu'un JSON libre ne permet pas proprement.
     */
    public function up(): void
    {
        Schema::create('field_options', function (Blueprint $table) {

            $table->id();

            $table->foreignId('form_field_id')
                  ->constrained('form_fields')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->string('value', 255);
            $table->string('label', 255);
            $table->integer('display_order')->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            // Configuration - tracabilite (voir LISEZMOI)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['form_field_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_options');
    }
};
