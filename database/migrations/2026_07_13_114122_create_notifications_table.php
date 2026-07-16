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
     * Cette migration ne correspondait plus du tout au Model
     * Notification (qui attend deja recipient_id / channel / status /
     * failure_reason). Realignee sur le Model :
     * - user_id -> recipient_id
     * - "type" (INFO/SUCCESS/WARNING/ERROR, un niveau de severite)
     *   remplace par "channel" (Email / In-App), qui est ce
     *   qu'exige BR-44. Le niveau de severite n'etait pas demande par
     *   le cahier des charges et est retire pour rester simple.
     * - is_read/read_at fusionnes dans "status"
     *   (Pending/Sent/Failed/Read), plus read_at conserve pour la
     *   date de lecture.
     * - Ajout de failure_reason (BR-47 : les echecs de notification
     *   sont journalises).
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Related Request (nullable: some notifications are not tied to one)
            $table->foreignId('request_id')
                  ->nullable()
                  ->constrained('requests')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            // Recipient
            $table->foreignId('recipient_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Content
            $table->string('title');
            $table->text('message');

            // Delivery
            $table->string('channel', 20);
            $table->string('status', 20)->default('Pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('failure_reason')->nullable();

            // Audit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
