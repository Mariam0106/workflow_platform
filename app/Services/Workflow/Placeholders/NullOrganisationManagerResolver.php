<?php

declare(strict_types=1);

namespace App\Services\Workflow\Placeholders;

use App\Contracts\Services\Workflow\OrganisationManagerResolverInterface;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * ==========================================================================
 * NullOrganisationManagerResolver (implementation TEMPORAIRE)
 * ==========================================================================
 *
 * A REMPLACER par le collegue quand le domaine Organisation aura une
 * vraie notion de "responsable de departement/entite". En attendant,
 * degrade proprement (retourne null, journalise un avertissement)
 * plutot que de planter le moteur de workflow si un Step utilise
 * ENTITY_MANAGER ou DEPARTMENT_MANAGER.
 *
 * Le binding de cette classe (dans AppServiceProvider) est a retirer le
 * jour ou l'implementation reelle est livree - chercher
 * "NullOrganisationManagerResolver" dans le code pour la localiser.
 * ==========================================================================
 */
class NullOrganisationManagerResolver implements OrganisationManagerResolverInterface
{
    public function managerOfDepartment(Department $department): ?User
    {
        Log::warning('OrganisationManagerResolverInterface::managerOfDepartment() appelee mais aucune implementation reelle n\'est encore branchee (voir NullOrganisationManagerResolver).', [
            'department_id' => $department->id,
        ]);

        return null;
    }

    public function managerOfEntity(Entity $entity): ?User
    {
        Log::warning('OrganisationManagerResolverInterface::managerOfEntity() appelee mais aucune implementation reelle n\'est encore branchee (voir NullOrganisationManagerResolver).', [
            'entity_id' => $entity->id,
        ]);

        return null;
    }
}
