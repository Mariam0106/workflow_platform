<?php

namespace Database\Seeders;

use App\Enums\ApplicationRoleCode;
use App\Enums\FormStatus;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\RequestStatus;
use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;
use App\Enums\ValidationDecision;
use App\Enums\ValidatorType;
use App\Enums\WorkflowPriority;
use App\Enums\WorkflowStatus;
use App\Models\ApplicationRole;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormField;
use App\Models\Notification;
use App\Models\Request;
use App\Models\RequestValue;
use App\Models\User;
use App\Models\Validation;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepHistory;
use App\Models\WorkflowTransition;
use App\ValueObjects\RequestReference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * ==========================================================================
 * DatabaseSeeder
 * ==========================================================================
 *
 * Etape 4 : jeu de donnees de reference realiste, couvrant :
 * - la hierarchie organisationnelle complete (Entity/Department/
 *   BusinessFunction/ApplicationRole/User, y compris le N+1 - BR-03/04/05/06)
 * - un Workflow PUBLIE a 4 Steps avec des Transitions conditionnelles
 *   (BR-21/22/23 : plusieurs Transitions possibles, priorite differente,
 *   condition sur le montant demande)
 * - un Formulaire PUBLIE avec des champs dynamiques (BR-56)
 * - 3 Requests illustrant les 3 etats terminaux/non-terminaux
 *   (Draft, Submitted en cours de validation, Completed avec historique
 *   complet, pieces jointes, notifications et audit)
 *
 * Sert de fixture de reference pour les tests du moteur de workflow
 * (Etape 9).
 * ==========================================================================
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ---------------------------------------------------------------
        // Organisation (BR-01 a BR-09)
        // ---------------------------------------------------------------

        $hq = Entity::factory()->create([
            'code' => 'HQ',
            'name' => 'Headquarters',
        ]);

        $itDept = Department::factory()->create([
            'entity_id' => $hq->id,
            'code' => 'IT',
            'name' => 'Information Technology',
        ]);

        $financeDept = Department::factory()->create([
            'entity_id' => $hq->id,
            'code' => 'FIN',
            'name' => 'Finance',
        ]);

        // Fonctions transverses (BR-05, exemples du cahier des charges :
        // Commercial / Credit Client / DAF / DG). Reutilisables partout,
        // pas rattachees a un seul departement (correction Etape 0).
        $fnDG = BusinessFunction::factory()->create(['code' => 'DG', 'name' => 'Direction Generale']);
        $fnDAF = BusinessFunction::factory()->create(['code' => 'DAF', 'name' => 'Direction Administrative et Financiere']);
        $fnCommercial = BusinessFunction::factory()->create(['code' => 'COM', 'name' => 'Commercial']);
        $fnCreditClient = BusinessFunction::factory()->create(['code' => 'CRC', 'name' => 'Credit Client']);

        $roleAdmin = ApplicationRole::factory()->administrator()->create();
        $roleValidator = ApplicationRole::factory()->validator()->create();
        $roleUser = ApplicationRole::factory()->create([
            'code' => ApplicationRoleCode::User->value,
            'name' => ApplicationRoleCode::User->label(),
        ]);

        // Hierarchie N+1 : CEO -> Finance Manager -> Employee (BR-03/04/05/06)
        $ceo = User::factory()->create([
            'entity_id' => $hq->id,
            'department_id' => $financeDept->id,
            'business_function_id' => $fnDG->id,
            'application_role_id' => $roleAdmin->id,
            'manager_id' => null,
            'first_name' => 'Amina',
            'last_name' => 'Benali',
            'email' => 'amina.benali@example.com',
        ]);

        $financeManager = User::factory()->create([
            'entity_id' => $hq->id,
            'department_id' => $financeDept->id,
            'business_function_id' => $fnDAF->id,
            'application_role_id' => $roleValidator->id,
            'manager_id' => $ceo->id,
            'first_name' => 'Karim',
            'last_name' => 'Idrissi',
            'email' => 'karim.idrissi@example.com',
        ]);

        $employee = User::factory()->create([
            'entity_id' => $hq->id,
            'department_id' => $itDept->id,
            'business_function_id' => $fnCommercial->id,
            'application_role_id' => $roleUser->id,
            'manager_id' => $financeManager->id,
            'first_name' => 'Sara',
            'last_name' => 'Toumi',
            'email' => 'sara.toumi@example.com',
        ]);

        $complianceOfficer = User::factory()->create([
            'entity_id' => $hq->id,
            'department_id' => $financeDept->id,
            'business_function_id' => $fnCreditClient->id,
            'application_role_id' => $roleValidator->id,
            'manager_id' => $financeManager->id,
            'first_name' => 'Youssef',
            'last_name' => 'Amrani',
            'email' => 'youssef.amrani@example.com',
        ]);

        // ---------------------------------------------------------------
        // Workflow : "Account Opening", publie, 4 Steps (BR-18 a BR-27)
        // ---------------------------------------------------------------

        $workflowCategory = WorkflowCategory::factory()->create([
            'code' => 'ACCOUNT',
            'name' => 'Account Management',
        ]);

        $workflow = Workflow::factory()->published()->create([
            'workflow_category_id' => $workflowCategory->id,
            'code' => 'WF-ACCOUNT-OPENING',
            'name' => 'Account Opening Approval',
            'version' => 1,
            'is_default' => true,
        ]);

        $stepManagerReview = WorkflowStep::factory()->start()->create([
            'workflow_id' => $workflow->id,
            'code' => 'MANAGER_REVIEW',
            'name' => 'Manager Review',
            'step_order' => 1,
            'validator_type' => ValidatorType::NPlus1,
            'validator_reference' => null,
        ]);

        $stepFinanceApproval = WorkflowStep::factory()->create([
            'workflow_id' => $workflow->id,
            'code' => 'FINANCE_APPROVAL',
            'name' => 'Finance Approval',
            'step_order' => 2,
            'validator_type' => ValidatorType::Role,
            'validator_reference' => $roleValidator->id,
        ]);

        $stepComplianceCheck = WorkflowStep::factory()->create([
            'workflow_id' => $workflow->id,
            'code' => 'COMPLIANCE_CHECK',
            'name' => 'Compliance Check',
            'step_order' => 3,
            'validator_type' => ValidatorType::Role,
            'validator_reference' => $roleValidator->id,
        ]);

        $stepCompleted = WorkflowStep::factory()->end()->create([
            'workflow_id' => $workflow->id,
            'code' => 'COMPLETED',
            'name' => 'Completed',
            'step_order' => 4,
            'validator_type' => ValidatorType::Role,
            'validator_reference' => null,
        ]);

        // ---------------------------------------------------------------
        // Formulaire : "Account Opening Form" (BR-10 a BR-17)
        // ---------------------------------------------------------------

        $formCategory = FormCategory::factory()->create([
            'code' => 'ACCOUNT_FORMS',
            'name' => 'Account Forms',
        ]);

        $form = Form::factory()->published()->create([
            'form_category_id' => $formCategory->id,
            'workflow_id' => $workflow->id,
            'code' => 'FORM-ACCOUNT-OPENING',
            'name' => 'Account Opening Request',
            'version' => 1,
        ]);

        $fieldClientName = FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Client Name',
            'technical_name' => 'client_name',
            'field_type' => 'text',
            'display_order' => 1,
        ]);

        $fieldAmount = FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Requested Amount',
            'technical_name' => 'amount',
            'field_type' => 'number',
            'display_order' => 2,
        ]);

        $fieldCountry = FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Country',
            'technical_name' => 'country',
            'field_type' => 'select',
            'options' => ['MA', 'FR', 'ES'],
            'display_order' => 3,
        ]);

        // ---------------------------------------------------------------
        // Transitions & Conditions (BR-20 a BR-23)
        //
        // Manager Review -> Finance Approval : toujours (pas de condition)
        // Finance Approval -> Compliance Check : SI amount >= 100000
        //     (priorite HIGH : evaluee avant la transition par defaut)
        // Finance Approval -> Completed : sinon (transition par defaut)
        // Compliance Check -> Completed : toujours
        // ---------------------------------------------------------------

        WorkflowTransition::factory()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $stepManagerReview->id,
            'to_step_id' => $stepFinanceApproval->id,
            'action_name' => 'send_to_finance',
            'priority' => WorkflowPriority::Medium->value,
            'is_default' => true,
        ]);

        $toCompliance = WorkflowTransition::factory()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $stepFinanceApproval->id,
            'to_step_id' => $stepComplianceCheck->id,
            'action_name' => 'escalate_to_compliance',
            'priority' => WorkflowPriority::High->value,
            'is_default' => false,
        ]);

        $toCompliance->transitionConditions()->create([
            'form_field_id' => $fieldAmount->id,
            'operator' => TransitionOperator::GreaterThanOrEqual,
            'expected_value' => '100000',
            'logical_operator' => TransitionLogicalOperator::And,
            'execution_order' => 1,
            'is_active' => true,
        ]);

        WorkflowTransition::factory()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $stepFinanceApproval->id,
            'to_step_id' => $stepCompleted->id,
            'action_name' => 'finalize',
            'priority' => WorkflowPriority::Low->value,
            'is_default' => true,
        ]);

        WorkflowTransition::factory()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $stepComplianceCheck->id,
            'to_step_id' => $stepCompleted->id,
            'action_name' => 'complete',
            'priority' => WorkflowPriority::Medium->value,
            'is_default' => true,
        ]);

        // ---------------------------------------------------------------
        // Requests : Draft / Submitted (en cours) / Completed (historique
        // complet + piece jointe + notifications + audit) - BR-28 a BR-42
        // ---------------------------------------------------------------

        // 1) Draft - modifiable librement (BR-30)
        Request::factory()->create([
            'form_id' => $form->id,
            'workflow_id' => $workflow->id,
            'requester_id' => $employee->id,
            'current_step_id' => $stepManagerReview->id,
            'reference_number' => RequestReference::generate(1, 2026),
            'status' => RequestStatus::Draft,
        ]);

        // 2) Submitted - en cours d'approbation Finance (BR-31 : lecture seule)
        $submittedRequest = Request::factory()->submitted()->create([
            'form_id' => $form->id,
            'workflow_id' => $workflow->id,
            'requester_id' => $employee->id,
            'current_step_id' => $stepFinanceApproval->id,
            'reference_number' => RequestReference::generate(2, 2026),
        ]);

        RequestValue::factory()->create(['request_id' => $submittedRequest->id, 'form_field_id' => $fieldClientName->id, 'value' => 'Global Trading SARL']);
        RequestValue::factory()->create(['request_id' => $submittedRequest->id, 'form_field_id' => $fieldAmount->id, 'value' => '45000']);
        RequestValue::factory()->create(['request_id' => $submittedRequest->id, 'form_field_id' => $fieldCountry->id, 'value' => 'MA']);

        WorkflowStepHistory::factory()->closed()->create([
            'request_id' => $submittedRequest->id,
            'workflow_step_id' => $stepManagerReview->id,
        ]);
        WorkflowStepHistory::factory()->create([
            'request_id' => $submittedRequest->id,
            'workflow_step_id' => $stepFinanceApproval->id,
        ]);

        Validation::factory()->create([
            'request_id' => $submittedRequest->id,
            'workflow_step_id' => $stepManagerReview->id,
            'validator_id' => $financeManager->id,
        ]);

        Notification::factory()->sent()->create([
            'request_id' => $submittedRequest->id,
            'recipient_id' => $financeManager->id,
            'title' => 'New request awaiting your approval',
            'channel' => NotificationChannel::Email,
        ]);

        // 3) Completed - montant eleve, a traverse les 4 Steps (illustre le
        // BR-23 de priorite : la Transition vers Compliance Check a ete
        // choisie car amount >= 100000)
        $completedRequest = Request::factory()->completed()->create([
            'form_id' => $form->id,
            'workflow_id' => $workflow->id,
            'requester_id' => $employee->id,
            'current_step_id' => $stepCompleted->id,
            'reference_number' => RequestReference::generate(3, 2026),
        ]);

        RequestValue::factory()->create(['request_id' => $completedRequest->id, 'form_field_id' => $fieldClientName->id, 'value' => 'Atlas Industries SA']);
        RequestValue::factory()->create(['request_id' => $completedRequest->id, 'form_field_id' => $fieldAmount->id, 'value' => '250000']);
        RequestValue::factory()->create(['request_id' => $completedRequest->id, 'form_field_id' => $fieldCountry->id, 'value' => 'FR']);

        foreach ([$stepManagerReview, $stepFinanceApproval, $stepComplianceCheck] as $step) {
            WorkflowStepHistory::factory()->closed()->create([
                'request_id' => $completedRequest->id,
                'workflow_step_id' => $step->id,
            ]);
        }
        WorkflowStepHistory::factory()->closed()->create([
            'request_id' => $completedRequest->id,
            'workflow_step_id' => $stepCompleted->id,
        ]);

        Validation::factory()->create(['request_id' => $completedRequest->id, 'workflow_step_id' => $stepManagerReview->id, 'validator_id' => $financeManager->id]);
        Validation::factory()->create(['request_id' => $completedRequest->id, 'workflow_step_id' => $stepFinanceApproval->id, 'validator_id' => $financeManager->id]);
        Validation::factory()->create(['request_id' => $completedRequest->id, 'workflow_step_id' => $stepComplianceCheck->id, 'validator_id' => $complianceOfficer->id]);

        Attachment::factory()->create([
            'request_id' => $completedRequest->id,
            'uploaded_by' => $employee->id,
            'original_name' => 'proof_of_funds.pdf',
        ]);

        Notification::factory()->sent()->create([
            'request_id' => $completedRequest->id,
            'recipient_id' => $employee->id,
            'title' => 'Your request has been approved',
            'channel' => NotificationChannel::InApp,
        ]);

        Notification::factory()->failed()->create([
            'request_id' => $completedRequest->id,
            'recipient_id' => $complianceOfficer->id,
            'title' => 'Compliance check required',
            'channel' => NotificationChannel::Email,
        ]);

        AuditLog::factory()->create([
            'user_id' => $employee->id,
            'action' => 'submitted',
            'entity_type' => 'Request',
            'entity_id' => $completedRequest->id,
        ]);
    }
}
