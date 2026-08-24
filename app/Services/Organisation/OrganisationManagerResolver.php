<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Services\Workflow\OrganisationManagerResolverInterface;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;

/**
 * ==========================================================================
 * OrganisationManagerResolver (implémentation réelle)
 * ==========================================================================
 *
 * Remplace NullOrganisationManagerResolver maintenant que
 * departments.manager_id / entities.manager_id existent (voir migration
 * add_manager_id_to_departments_and_entities). Un Département ou une
 * Entité sans Responsable désigné reste possible (colonne nullable) -
 * dans ce cas, ces méthodes retournent null exactement comme le
 * placeholder, mais uniquement pour CE cas précis, pas systématiquement.
 * ==========================================================================
 */
class OrganisationManagerResolver implements OrganisationManagerResolverInterface
{
    public function managerOfDepartment(Department $department): ?User
    {
        $manager = $department->manager;

        return ($manager !== null && $manager->is_active) ? $manager : null;
    }

    public function managerOfEntity(Entity $entity): ?User
    {
        $manager = $entity->manager;

        return ($manager !== null && $manager->is_active) ? $manager : null;
    }
}
