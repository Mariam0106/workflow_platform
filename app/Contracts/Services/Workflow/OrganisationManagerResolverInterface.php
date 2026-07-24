<?php

declare(strict_types=1);

namespace App\Contracts\Services\Workflow;

use App\Models\Department;
use App\Models\Entity;
use App\Models\User;

/**
 * ==========================================================================
 * OrganisationManagerResolverInterface
 * ==========================================================================
 *
 * POINT D'INTEGRATION AVEC LE DOMAINE ORGANISATION.
 *
 * Le domaine Workflow a besoin de savoir "qui est le responsable de ce
 * Department / cette Entity" pour les strategies de validateur
 * ENTITY_MANAGER / DEPARTMENT_MANAGER (BR-59), mais cette notion de
 * "responsable" appartient au domaine Organisation (aucune colonne
 * manager_id n'existe aujourd'hui sur departments/entities - a la
 * difference de users.manager_id qui, lui, existe deja).
 *
 * Conformement au sens unique de dependance (Workflow -> Organisation,
 * jamais l'inverse) et aux regles de repartition de l'equipe, c'est le
 * domaine WORKFLOW qui definit ce contrat (il en a besoin), et c'est le
 * domaine ORGANISATION qui devra fournir l'implementation reelle (par
 * exemple, si un jour departments/entities recoivent une colonne
 * "manager_id", ou toute autre logique metier decidee par le collegue).
 *
 * En attendant cette implementation reelle, un binding temporaire
 * (NullOrganisationManagerResolver) est fourni : il retourne toujours
 * null, ce qui degrade proprement (aucun validateur trouve pour ces 2
 * strategies) plutot que de planter.
 * ==========================================================================
 */
interface OrganisationManagerResolverInterface
{
    public function managerOfDepartment(Department $department): ?User;

    public function managerOfEntity(Entity $entity): ?User;
}
