<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\Contracts\Repositories\Organisation\UserRepositoryInterface;
use App\DataTransferObjects\Organisation\CreateUserData;
use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Enums\ApplicationRoleCode;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\RegistrationStatus;
use App\Events\Organisation\UserCreated;
use App\Exceptions\Organisation\DepartmentNotFoundException;
use App\Exceptions\Organisation\EntityNotFoundException;
use App\Exceptions\Organisation\InvalidManagerAssignmentException;
use App\Exceptions\Organisation\RegistrationNotPendingException;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;
use App\Models\ApplicationRole;
use App\Models\Notification;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * ==========================================================================
 * UserService
 * ==========================================================================
 *
 * Every write path for a User goes through here - Controllers never call
 * UserRepository::createFromData()/updateFromData() directly. This is where
 * password hashing, authorization (via PermissionService), and hierarchy
 * integrity (no cyclical N+1 chains) are enforced in exactly one place.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03/04/05/06  Entity/Department/BusinessFunction/ApplicationRole are
 *                 mandatory and must référence active records (BR-09).
 * BR-07           Only active Users may log in (enforced at auth time,
 *                 not here - see LoginRequest::authenticate()).
 * BR-69           Chaque action est journalisée - le mot de passe (haché
 *                 ou non) n'est JAMAIS écrit dans le Journal d'Audit,
 *                 même par accident : chaque appel à AuditLogger::log()
 *                 ci-dessous liste explicitement les champs autorisés,
 *                 jamais un toArray() brut du DTO.
 * ==========================================================================
 */
class UserService
{
    /**
     * @var list<string> champs sûrs à journaliser pour un Utilisateur -
     *      liste blanche explicite : ni le mot de passe, ni son hash, ne
     *      doivent jamais apparaître dans le Journal d'Audit (BR-69).
     */
    private const AUDITABLE_FIELDS = [
        'entity_id', 'department_id', 'business_function_id',
        'default_application_role_id', 'manager_id',
        'first_name', 'last_name', 'email', 'phone',
        'is_active', 'employee_number', 'job_title',
    ];

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly DepartmentRepositoryInterface $departments,
        private readonly EntityRepositoryInterface $entities,
        private readonly PermissionService $permissions,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * Self-registration (Jalon J1) - no permission check, anyone with a
     * valid company email may sign up. Kept separate from createByAdmin()
     * so the intent is unambiguous at the call site.
     */
    public function register(CreateUserData $dto): User
    {
        // BR-06/09 (sécurité) : une auto-inscription publique ne doit
        // JAMAIS pouvoir s'attribuer elle-même un Rôle Applicatif -
        // forcé ici sur "User" (le plus bas privilège), quel que soit ce
        // que le formulaire aurait transmis. Un Administrateur choisit
        // le/les vrai(s) Rôle(s) au moment de l'approbation
        // (approveRegistration()). Le compte reste par ailleurs
        // inutilisable (is_active=false, registration_status Pending)
        // tant que cette approbation n'a pas eu lieu.
        $userRoleId = ApplicationRole::query()->where('code', ApplicationRoleCode::User->value)->value('id');

        $pendingDto = new CreateUserData(
            entityId: $dto->entityId,
            departmentId: $dto->departmentId,
            businessFunctionId: $dto->businessFunctionId,
            applicationRoleIds: [$userRoleId],
            defaultApplicationRoleId: $userRoleId,
            managerId: $dto->managerId,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            email: $dto->email,
            phone: $dto->phone,
            password: $dto->password,
            isActive: false,
            registrationStatus: RegistrationStatus::Pending,
            employeeNumber: $dto->employeeNumber,
            jobTitle: $dto->jobTitle,
        );

        $user = $this->createUser($pendingDto, actor: null);

        foreach (User::query()->whereHas('applicationRoles', fn ($q) => $q->where('code', ApplicationRoleCode::Administrator->value))->where('is_active', true)->get() as $admin) {
            Notification::create([
                'recipient_id' => $admin->id,
                'title' => "Nouvelle demande d'inscription",
                'message' => "{$user->full_name} ({$user->email}) demande à rejoindre la plateforme.",
                'channel' => NotificationChannel::InApp,
                'status' => NotificationStatus::Sent,
            ]);
        }

        return $user;
    }

    /**
     * Admin-initiated creation.
     *
     * @throws UnauthorizedActionException
     */
    public function createByAdmin(CreateUserData $dto, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        return $this->createUser($dto, $actor);
    }

    /**
     * @param User|null $actor null pour une auto-inscription (BR-69 :
     *        l'acteur journalisé est alors le nouvel Utilisateur
     *        lui-même, seul "auteur" possible de sa propre inscription).
     */
    private function createUser(CreateUserData $dto, ?User $actor): User
    {
        $this->assertEntityAndDepartmentAreActive($dto->entityId, $dto->departmentId);

        if ($dto->managerId !== null) {
            $this->assertManagerAssignmentIsValid(userId: null, managerId: $dto->managerId);
        }

        // Hash centralisé ici : le Repository/DTO transportent le mot de
        // passe en clair jusque-là (voir CreateUserData). Le cast 'hashed'
        // du Model est idempotent, donc pas de double-hash même si ce
        // Service est un jour contourné.
        $user = $this->users->createFromData(
            new CreateUserData(
                entityId: $dto->entityId,
                departmentId: $dto->departmentId,
                businessFunctionId: $dto->businessFunctionId,
                applicationRoleIds: $dto->applicationRoleIds,
                defaultApplicationRoleId: $dto->defaultApplicationRoleId,
                managerId: $dto->managerId,
                firstName: $dto->firstName,
                lastName: $dto->lastName,
                email: $dto->email,
                phone: $dto->phone,
                password: Hash::make($dto->password),
                isActive: $dto->isActive,
                registrationStatus: $dto->registrationStatus,
                employeeNumber: $dto->employeeNumber,
                jobTitle: $dto->jobTitle,
            )
        );

        // Étape 9 : émis ici, jamais depuis le Repository (le Repository
        // ne connaît pas les événements métier) - couvre les deux
        // parcours (self-registration J1 et créationByAdmin, Étape 13),
        // puisque tous deux passent par createUser().
        UserCreated::dispatch($user);

        $this->auditLogger->log(
            userId: $actor?->id ?? $user->id,
            action: $actor === null ? 'user_registered' : 'user_created',
            entityType: 'User',
            entityId: $user->id,
            newValues: array_intersect_key($user->getAttributes(), array_flip(self::AUDITABLE_FIELDS)),
        );

        return $user;
    }

    /**
     * Self-service profile update - a User may always edit their own
     * basic info, regardless of role. Deliberately narrow : anything
     * touching entity/department/role/manager/is_active is rejected here,
     * even if present in $dto - use updateByAdmin() for that.
     *
     * @throws UnauthorizedActionException
     */
    public function updateOwnProfile(int $userId, UpdateUserData $dto, User $actor): User
    {
        if (! $this->permissions->canManageOwnProfile($actor, $userId)) {
            throw UnauthorizedActionException::requiresRole('SELF', $actor->id);
        }

        $safeDto = UpdateUserData::fromArray(array_intersect_key(
            $dto->toArray(),
            array_flip(['first_name', 'last_name', 'phone']),
        ));

        $before = $this->users->findById($userId);
        $oldValues = array_intersect_key($before->getAttributes(), array_flip(['first_name', 'last_name', 'phone']));

        $user = $this->users->updateFromData($userId, $safeDto);

        $this->auditLogger->log($actor->id, 'user_profile_updated', 'User', $user->id, $oldValues, $safeDto->toArray());

        return $user;
    }

    /**
     * Admin-initiated update - full access to every field, including
     * hierarchy changes (guarded against cycles).
     *
     * @throws UnauthorizedActionException
     * @throws InvalidManagerAssignmentException
     */
    public function updateByAdmin(int $userId, UpdateUserData $dto, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        if (array_key_exists('manager_id', $dto->toArray())) {
            $this->assertManagerAssignmentIsValid($userId, $dto->managerId);
        }

        if ($dto->departmentId !== null || $dto->entityId !== null) {
            $current = $this->users->findById($userId);
            $this->assertEntityAndDepartmentAreActive(
                $dto->entityId ?? $current->entity_id,
                $dto->departmentId ?? $current->department_id,
            );
        }

        $before = $this->users->findById($userId);
        $oldValues = array_intersect_key($before->getAttributes(), array_flip(self::AUDITABLE_FIELDS));

        $user = $this->users->updateFromData($userId, $dto);

        $this->auditLogger->log(
            $actor->id,
            'user_updated',
            'User',
            $user->id,
            $oldValues,
            array_intersect_key($dto->toArray(), array_flip(self::AUDITABLE_FIELDS)),
        );

        return $user;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function deactivate(int $userId, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        if ($actor->id === $userId) {
            throw UnauthorizedActionException::requiresRole('ANOTHER_ADMIN', $actor->id);
        }

        $user = $this->users->findById($userId);
        $user->is_active = false;
        $user = $this->users->save($user);

        $this->auditLogger->log($actor->id, 'user_deactivated', 'User', $user->id);

        return $user;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function activate(int $userId, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $user = $this->users->findById($userId);
        $user->is_active = true;
        $user = $this->users->save($user);

        $this->auditLogger->log($actor->id, 'user_activated', 'User', $user->id);

        return $user;
    }

    /**
     * BR-09 : archived Departments/Entities cannot receive new Users.
     */
    private function assertEntityAndDepartmentAreActive(int $entityId, int $departmentId): void
    {
        $entity = $this->entities->findById($entityId);
        $department = $this->departments->findById($departmentId);

        // findById() already throws *NotFoundException if missing - here
        // we additionally check "active", which is a different failure
        // mode (exists but archived), so a dedicated, clearer message
        // matters for whoever reads the error.
        if (! $entity->isActive()) {
            throw EntityNotFoundException::archived($entityId);
        }

        if (! $department->isActive()) {
            throw DepartmentNotFoundException::archived($departmentId);
        }
    }

    /**
     * Prevents self-assignment and cyclical N+1 chains (A manages B
     * manages A). $userId is null for a brand-new User (can't create a
     * cycle with someone who doesn't exist yet, but self-assignment via
     * a race is still impossible since the id doesn't exist).
     *
     * @throws InvalidManagerAssignmentException
     */
    private function assertManagerAssignmentIsValid(?int $userId, ?int $managerId): void
    {
        if ($managerId === null) {
            return;
        }

        if ($userId !== null && $managerId === $userId) {
            throw InvalidManagerAssignmentException::selfAssignment($userId);
        }

        if ($userId === null) {
            return;
        }

        // Walk up the proposed manager's own chain - if $userId shows up,
        // assigning $managerId to $userId would close a loop.
        $current = $this->users->findById($managerId);
        $visited = [];

        while ($current->manager_id !== null) {
            if ($current->manager_id === $userId || in_array($current->manager_id, $visited, true)) {
                throw InvalidManagerAssignmentException::wouldCreateCycle($userId, $managerId);
            }

            $visited[] = $current->manager_id;
            $current = $this->users->findById($current->manager_id);
        }
    }

    /**
     * Approuve une auto-inscription en attente : applique les
     * éventuels ajustements de l'Administrateur (Entité/Département/
     * Fonction Métier/Rôle(s) - il peut très bien garder tel quel ce
     * que la personne a demandé), active le compte, et envoie le
     * véritable e-mail de confirmation.
     */
    public function approveRegistration(int $userId, UpdateUserData $adjustments, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $user = $this->users->findById($userId);

        if (! $user->isPendingRegistration()) {
            throw RegistrationNotPendingException::forUser($user);
        }

        $user = $this->updateByAdmin($userId, $adjustments, $actor);

        $user->is_active = true;
        $user->registration_status = RegistrationStatus::Approved;
        $user->approved_at = now();
        $user->approved_by = $actor->id;
        $user->save();

        $this->auditLogger->log($actor->id, 'registration_approved', 'User', $user->id, newValues: ['registration_status' => 'Approved']);

        Mail::to((string) $user->email)->queue(new RegistrationApprovedMail($user));

        return $user;
    }

    /**
     * Refuse une auto-inscription en attente - le compte reste
     * définitivement inutilisable (is_active=false), conservé pour
     * historique plutôt que supprimé.
     */
    public function rejectRegistration(int $userId, ?string $reason, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $user = $this->users->findById($userId);

        if (! $user->isPendingRegistration()) {
            throw RegistrationNotPendingException::forUser($user);
        }

        $user->registration_status = RegistrationStatus::Rejected;
        $user->rejected_reason = $reason;
        $user->approved_by = $actor->id;
        $user->approved_at = now();
        $user->save();

        $this->auditLogger->log($actor->id, 'registration_rejected', 'User', $user->id, newValues: ['registration_status' => 'Rejected', 'reason' => $reason]);

        Mail::to((string) $user->email)->queue(new RegistrationRejectedMail($user));

        return $user;
    }
}