<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Exceptions\DomainException;

/**
 * Base exception for the Workflow module (Workflows, Steps, Transitions,
 * Forms, Requests, Validations).
 *
 * Every Workflow exception extends this class instead of DomainException
 * directly - it exists so that `catch (WorkflowEngineException $e)` lets
 * a caller catch "anything Workflow-related" in one line, without ever
 * catching an Organisation exception by accident. Mirrors
 * App\Exceptions\Organisation\OrganisationException on the other domain.
 */
abstract class WorkflowEngineException extends DomainException {}
