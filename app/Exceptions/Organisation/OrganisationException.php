<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

use App\Exceptions\DomainException;

/**
 * Base exception for the Organisation module (Users, Departments, Entities,
 * Business Functions, Application Roles).
 *
 * Every Organisation exception extends this class instead of DomainException
 * directly - it exists so that `catch (OrganisationException $e)` lets a
 * caller catch "anything Organisation-related" in one line, without ever
 * catching a Workflow exception by accident.
 */
abstract class OrganisationException extends DomainException {}
