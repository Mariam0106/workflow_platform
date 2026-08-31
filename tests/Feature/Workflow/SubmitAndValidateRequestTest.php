<?php

declare(strict_types=1);

namespace Tests\Feature\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Enums\FormStatus;
use App\Enums\ValidatorType;
use App\Enums\WorkflowStatus;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormField;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ==========================================================================
 * SubmitAndValidateRequestTest
 * ==========================================================================
 *
 * Test Feature de bout en bout : vraie pile HTTP (routes, middleware
 * auth, Form Requests, Controllers, Policies, WorkflowEngineService,
 * Events/Listeners) - pas des tests unitaires isoles. Couvre les BR
 * critiques du moteur (BR-28, 31/32, 36, 38, 39, 41) via de vraies
 * requetes HTTP plutot que des appels directs au Service.
 * ==========================================================================
 */
class SubmitAndValidateRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $requester;

    private User $manager;

    private Form $form;

    private FormField $amountField;

    private WorkflowStep $startStep;

    private WorkflowStep $endStep;

    protected function setUp(): void
    {
        parent::setUp();

        $entity = Entity::factory()->create();
        $department = Department::factory()->create(['entity_id' => $entity->id]);
        $businessFunction = BusinessFunction::factory()->create();
        $userRole = ApplicationRole::factory()->create(['code' => ApplicationRoleCode::User->value]);
        $validatorRole = ApplicationRole::factory()->validator()->create();

        $this->manager = User::factory()->create([
            'entity_id' => $entity->id,
            'department_id' => $department->id,
            'business_function_id' => $businessFunction->id,
            'default_application_role_id' => $validatorRole->id,
        ]);

        $this->requester = User::factory()->create([
            'entity_id' => $entity->id,
            'department_id' => $department->id,
            'business_function_id' => $businessFunction->id,
            'default_application_role_id' => $userRole->id,
            'manager_id' => $this->manager->id,
        ]);

        $workflowCategory = WorkflowCategory::factory()->create();
        $workflow = Workflow::factory()->published()->create(['workflow_category_id' => $workflowCategory->id]);

        $this->startStep = WorkflowStep::factory()->start()->create([
            'workflow_id' => $workflow->id,
            'step_order' => 1,
            'validator_type' => ValidatorType::NPlus1,
        ]);

        $this->endStep = WorkflowStep::factory()->end()->create([
            'workflow_id' => $workflow->id,
            'step_order' => 2,
            // BR-06 : chaque Étape - y compris l'Étape de fin - a son
            // propre Validateur et doit recevoir sa propre validation
            // avant que le Workflow ne se termine (voir
            // WorkflowEngineService::advanceToNextStep()). Test de
            // non-régression : réutilise volontairement le même
            // $this->manager comme Validateur sur les deux Étapes,
            // pour garantir qu'une seule validation par Étape ne
            // clôture jamais prématurément le Workflow.
            'validator_type' => ValidatorType::User,
            'validator_reference' => $this->manager->id,
        ]);

        WorkflowTransition::factory()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $this->startStep->id,
            'to_step_id' => $this->endStep->id,
            'is_default' => true,
        ]);

        $formCategory = FormCategory::factory()->create();
        $this->form = Form::factory()->published()->create([
            'form_category_id' => $formCategory->id,
            'workflow_id' => $workflow->id,
        ]);

        $this->amountField = FormField::factory()->create(['form_id' => $this->form->id]);
    }

    public function test_a_user_can_submit_a_request(): void
    {
        $response = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.status', 'Submitted');
        $this->assertDatabaseHas('requests', [
            'form_id' => $this->form->id,
            'requester_id' => $this->requester->id,
        ]);
    }

    public function test_submitting_from_an_unpublished_form_fails_with_a_clean_error(): void
    {
        $draftForm = Form::factory()->create([
            'form_category_id' => $this->form->form_category_id,
            'workflow_id' => $this->form->workflow_id,
            'status' => FormStatus::Draft,
        ]);

        $response = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $draftForm->id,
            'values' => [$this->amountField->id => '1000'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'form_not_published');
    }

    public function test_only_the_assigned_validator_can_record_a_decision(): void
    {
        $stranger = User::factory()->create([
            'entity_id' => $this->requester->entity_id,
            'department_id' => $this->requester->department_id,
            'business_function_id' => $this->requester->business_function_id,
            'default_application_role_id' => $this->requester->default_application_role_id,
        ]);

        $submit = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);

        $requestId = $submit->json('data.id');

        $response = $this->actingAs($stranger)->postJson("/workflow/requests/{$requestId}/validations", [
            'decision' => 'Approved',
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('error', 'unauthorized_action');
    }

    public function test_rejecting_a_request_ends_the_workflow_immediately(): void
    {
        $submit = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);
        $requestId = $submit->json('data.id');

        $response = $this->actingAs($this->manager)->postJson("/workflow/requests/{$requestId}/validations", [
            'decision' => 'Rejected',
            'comment' => 'Incomplete file',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('requests', ['id' => $requestId, 'status' => 'Rejected']);
    }

    public function test_rejecting_without_a_comment_is_rejected_by_validation(): void
    {
        $submit = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);
        $requestId = $submit->json('data.id');

        $response = $this->actingAs($this->manager)->postJson("/workflow/requests/{$requestId}/validations", [
            'decision' => 'Rejected',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['comment']);
    }

    public function test_approving_the_final_step_completes_the_request(): void
    {
        $submit = $this->actingAs($this->requester)->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);
        $requestId = $submit->json('data.id');

        // Étape 1 (N+1, résolue vers $this->manager) : approuver ici ne
        // fait qu'avancer vers l'Étape 2 - BR-06, chaque Étape (y
        // compris l'Étape de fin) a son propre Validateur et doit
        // recevoir SA PROPRE validation avant que le Workflow ne se
        // termine (voir WorkflowEngineService::advanceToNextStep()).
        $firstApproval = $this->actingAs($this->manager)->postJson("/workflow/requests/{$requestId}/validations", [
            'decision' => 'Approved',
        ]);

        $firstApproval->assertStatus(201);
        $this->assertDatabaseHas('requests', ['id' => $requestId, 'status' => 'Submitted']);
        $this->assertDatabaseHas('requests', ['id' => $requestId, 'current_step_id' => $this->endStep->id]);

        // Étape 2 (fin) : le Validateur est explicitement le même
        // $this->manager que sur l'Étape 1 - garantit qu'une seule
        // validation par Étape ne clôture jamais prématurément le
        // Workflow, même quand le même Utilisateur valide les deux.
        // Seule cette seconde validation doit clore le Workflow.
        $response = $this->actingAs($this->manager)->postJson("/workflow/requests/{$requestId}/validations", [
            'decision' => 'Approved',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('requests', ['id' => $requestId, 'status' => 'Completed']);
        $this->assertDatabaseHas('notifications', ['recipient_id' => $this->requester->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'workflow_finished', 'entity_id' => $requestId]);
    }

    public function test_a_guest_cannot_submit_a_request(): void
    {
        $response = $this->postJson('/workflow/requests', [
            'form_id' => $this->form->id,
            'values' => [$this->amountField->id => '1000'],
        ]);

        $response->assertStatus(401);
    }
}
