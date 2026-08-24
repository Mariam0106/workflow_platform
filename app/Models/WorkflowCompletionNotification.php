<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * ==========================================================================
 * WorkflowCompletionNotification
 * ==========================================================================
 *
 * Un destinataire supplémentaire (Fonction Métier ou Utilisateur) à
 * notifier quand une Demande de ce Workflow est entièrement approuvée -
 * en plus du Demandeur, toujours notifié par défaut. Voir la migration
 * pour le contexte métier complet.
 * ==========================================================================
 */
class WorkflowCompletionNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'notify_type',
        'notify_reference',
        'created_by',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isBusinessFunction(): bool
    {
        return $this->notify_type === 'BUSINESS_FUNCTION';
    }

    public function isUser(): bool
    {
        return $this->notify_type === 'USER';
    }

    /**
     * Nom lisible de la cible (pour l'affichage dans le BackOffice).
     */
    public function referenceLabel(): ?string
    {
        return $this->isBusinessFunction()
            ? BusinessFunction::find($this->notify_reference)?->name
            : User::find($this->notify_reference)?->full_name;
    }

    /**
     * Résout les vrais Utilisateurs concernés par cette configuration -
     * un(e) seul(e) pour "Utilisateur désigné", potentiellement
     * plusieurs pour "Fonction Métier" (tous ceux qui l'occupent).
     *
     * @return Collection<int, User>
     */
    public function resolveRecipients(): Collection
    {
        if ($this->isBusinessFunction()) {
            return User::query()
                ->where('business_function_id', $this->notify_reference)
                ->where('is_active', true)
                ->get();
        }

        $user = User::query()->where('id', $this->notify_reference)->where('is_active', true)->first();

        return $user ? collect([$user]) : collect();
    }
}
