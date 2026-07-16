<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * ValidatorType Enum
 * ==========================================================================
 *
 * BR-59 : the Workflow Engine must determine the next validator generically,
 * from configuration - never from hard-coded conditions. This enum is the
 * closed list of resolution strategies the ValidatorResolverService (Etape 9)
 * will support. workflow_steps.validator_reference is interpreted
 * differently depending on this type:
 *
 * Role               -> validator_reference = application_roles.id
 *                       (anyone with that Role, scoped to the requester's
 *                       Department/Entity)
 * User               -> validator_reference = users.id (one specific User)
 * NPlus1             -> validator_reference is ignored ; resolves to
 *                       $request->requester->manager
 * EntityManager      -> resolves to the manager of the requester's Entity
 * DepartmentManager  -> resolves to the manager of the requester's Department
 */
enum ValidatorType: string
{
    case Role = 'ROLE';
    case User = 'USER';
    case NPlus1 = 'N_PLUS_1';
    case EntityManager = 'ENTITY_MANAGER';
    case DepartmentManager = 'DEPARTMENT_MANAGER';
}
