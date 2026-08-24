<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\FormPriority;
use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;
use App\Enums\ValidatorType;
use App\Enums\WorkflowPriority;
use App\Models\BusinessFunction;
use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\FormField;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ==========================================================================
 * SeedRealAccountWorkflowsCommand
 * ==========================================================================
 *
 * `php artisan app:seed-real-account-workflows` - crée les DEUX vrais
 * Formulaires/Workflows "Comptes Clients" (Ouverture / Modification),
 * à partir des documents métier réels fournis. Créés en BROUILLON
 * uniquement : rien n'est publié ni utilisable par un vrai demandeur
 * tant qu'un Administrateur ne vérifie pas et ne clique pas
 * lui-même sur "Publier" depuis l'interface.
 *
 * Idempotent : peut être relancée sans risque (firstOrCreate sur les
 * Fonctions Métier/Catégories ; les Formulaires/Workflows eux-mêmes ne
 * sont recréés que s'ils n'existent pas déjà sous ce code).
 *
 * Hypothèses posées (à ajuster librement depuis l'interface ensuite) :
 * - Seuil de routage DAF/DG : DAF sous le seuil, DG au-dessus
 *   (500 KMAD Weber-Isover / 1 MMAD LPM), confirmé par le client.
 * - "Propriétaires/Associés" et "Directeurs/Gérants" : zones de texte
 *   libre (pas de liste répétable dans l'app), confirmé par le client.
 * - "Forme juridique" : texte libre, confirmé par le client.
 * - "Lu et approuvé / Cachet / Signature" : non modélisé en champ de
 *   formulaire - la signature reste papier (voir la fonctionnalité
 *   Pièces Jointes : le Validateur imprime le document pour signature
 *   physique, hors périmètre applicatif).
 * ==========================================================================
 */
class SeedRealAccountWorkflowsCommand extends Command
{
    protected $signature = 'app:seed-real-account-workflows';

    protected $description = "Crée les vrais Formulaires/Workflows Ouverture et Modification de compte client (en brouillon)";

    public function handle(): int
    {
        $admin = User::query()
            ->whereHas('applicationRoles', fn ($q) => $q->where('code', 'ADMIN'))
            ->where('is_active', true)
            ->first();

        if ($admin === null) {
            $this->error("Aucun Administrateur actif trouvé - crée-le d'abord avec `php artisan app:create-admin`.");

            return self::FAILURE;
        }

        DB::transaction(function () use ($admin) {
            $functions = $this->ensureBusinessFunctions();
            $workflowCategory = WorkflowCategory::firstOrCreate(
                ['code' => 'COMPTES_CLIENTS'],
                ['name' => 'Comptes Clients', 'created_by' => $admin->id],
            );
            $formCategory = FormCategory::firstOrCreate(
                ['code' => 'COMPTES_CLIENTS'],
                ['name' => 'Comptes Clients', 'created_by' => $admin->id],
            );

            $this->buildOuverture($admin, $functions, $workflowCategory, $formCategory);
            $this->buildModification($admin, $functions, $workflowCategory, $formCategory);
        });

        $this->info('Formulaires et Workflows "Ouverture" et "Modification" de compte client créés en BROUILLON.');
        $this->line('Vérifie-les depuis Workflow → Formulaires / Workflow, ajuste si besoin, puis publie-les toi-même.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, BusinessFunction>
     */
    private function ensureBusinessFunctions(): array
    {
        $definitions = [
            'COMMERCIAL' => 'Commercial',
            'DCM' => 'DCM',
            'CREDIT_CLIENT' => 'Crédit Client',
            'DAF' => 'DAF',
            'DG' => 'DG',
        ];

        $functions = [];

        foreach ($definitions as $code => $name) {
            $functions[$code] = BusinessFunction::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }

        return $functions;
    }

    /**
     * @param array<string, BusinessFunction> $fn
     */
    private function buildOuverture(User $admin, array $fn, WorkflowCategory $workflowCategory, FormCategory $formCategory): void
    {
        if (Workflow::where('code', 'WF-OUVERTURE-COMPTE')->exists()) {
            $this->warn('WF-OUVERTURE-COMPTE existe déjà - ignoré (relance sans risque).');

            return;
        }

        $workflow = Workflow::create([
            'workflow_category_id' => $workflowCategory->id,
            'code' => 'WF-OUVERTURE-COMPTE',
            'name' => 'Ouverture de compte client',
            'version' => 1,
            'is_default' => true,
            'created_by' => $admin->id,
        ]);

        $steps = $this->buildAccountSteps($workflow, $fn);
        $form = $this->buildAccountForm(
            $admin,
            $formCategory,
            $workflow,
            code: 'FORM-OUVERTURE-COMPTE',
            name: "Demande d'ouverture de compte client",
        );

        $this->addOuvertureFields($admin, $form);
        $this->buildAccountTransitions($workflow, $steps, $form, marqueValues: ['WEBER_ISOVER', 'LPM']);

        // Le Demandeur (Commercial) est déjà notifié par défaut - Crédit
        // Client doit l'être en plus, puisque c'est lui qui va
        // réellement ouvrir le compte dans le système métier, sans
        // jamais être Validateur de l'Étape finale (DAF/DG).
        $workflow->completionNotifications()->create([
            'notify_type' => 'BUSINESS_FUNCTION',
            'notify_reference' => $fn['CREDIT_CLIENT']->id,
            'created_by' => $admin->id,
        ]);

        $this->info("Workflow « Ouverture de compte client » créé (id {$workflow->id}), Formulaire (id {$form->id}).");
    }

    /**
     * @param array<string, BusinessFunction> $fn
     */
    private function buildModification(User $admin, array $fn, WorkflowCategory $workflowCategory, FormCategory $formCategory): void
    {
        if (Workflow::where('code', 'WF-MODIFICATION-COMPTE')->exists()) {
            $this->warn('WF-MODIFICATION-COMPTE existe déjà - ignoré (relance sans risque).');

            return;
        }

        $workflow = Workflow::create([
            'workflow_category_id' => $workflowCategory->id,
            'code' => 'WF-MODIFICATION-COMPTE',
            'name' => 'Modification de compte client',
            'version' => 1,
            'is_default' => true,
            'created_by' => $admin->id,
        ]);

        $steps = $this->buildAccountSteps($workflow, $fn);
        $form = $this->buildAccountForm(
            $admin,
            $formCategory,
            $workflow,
            code: 'FORM-MODIFICATION-COMPTE',
            name: 'Demande de modification de compte client',
        );

        $this->addModificationFields($admin, $form);
        $this->buildAccountTransitions($workflow, $steps, $form, marqueValues: ['WEBER', 'ISOVER', 'LPM']);

        $workflow->completionNotifications()->create([
            'notify_type' => 'BUSINESS_FUNCTION',
            'notify_reference' => $fn['CREDIT_CLIENT']->id,
            'created_by' => $admin->id,
        ]);

        $this->info("Workflow « Modification de compte client » créé (id {$workflow->id}), Formulaire (id {$form->id}).");
    }

    /**
     * Les deux Workflows partagent exactement la même charpente d'Étapes
     * (Analyse DCM → Validation Crédit Client → DAF ou DG selon seuil).
     * DAF et DG sont directement les Étapes de fin (BR-34 : "au moins
     * une", plusieurs sont donc permises) - l'approbation DAF/DG EST la
     * décision finale dans le processus réel, il n'y a pas de nouvelle
     * approbation à obtenir après coup pour "clôturer". Crédit Client
     * (qui va réellement ouvrir/modifier le compte dans le système
     * métier) est prévenu par notification de clôture plutôt que par
     * une Étape de validation supplémentaire qu'il n'aurait aucune
     * raison de "valider" une seconde fois.
     *
     * @param array<string, BusinessFunction> $fn
     * @return array<string, WorkflowStep>
     */
    private function buildAccountSteps(Workflow $workflow, array $fn): array
    {
        $dcm = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'code' => 'DCM_REVIEW',
            'name' => 'Analyse DCM',
            'step_order' => 1,
            'is_start' => true,
            'validator_type' => ValidatorType::BusinessFunction,
            'validator_reference' => $fn['DCM']->id,
        ]);

        $creditClient = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'code' => 'CREDIT_CLIENT_REVIEW',
            'name' => 'Validation Crédit Client',
            'step_order' => 2,
            'validator_type' => ValidatorType::BusinessFunction,
            'validator_reference' => $fn['CREDIT_CLIENT']->id,
        ]);

        $daf = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'code' => 'DAF_APPROVAL',
            'name' => 'Approbation DAF',
            'step_order' => 3,
            'is_end' => true,
            'validator_type' => ValidatorType::BusinessFunction,
            'validator_reference' => $fn['DAF']->id,
        ]);

        $dg = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'code' => 'DG_APPROVAL',
            'name' => 'Approbation DG',
            'step_order' => 4,
            'is_end' => true,
            'validator_type' => ValidatorType::BusinessFunction,
            'validator_reference' => $fn['DG']->id,
        ]);

        return ['dcm' => $dcm, 'credit_client' => $creditClient, 'daf' => $daf, 'dg' => $dg];
    }

    private function buildAccountForm(User $admin, FormCategory $formCategory, Workflow $workflow, string $code, string $name): Form
    {
        return Form::create([
            'form_category_id' => $formCategory->id,
            'workflow_id' => $workflow->id,
            'code' => $code,
            'name' => $name,
            'version' => 1,
            'priority' => FormPriority::Normal,
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Seuil de routage DAF/DG confirmé par le client : DAF sous le
     * seuil, DG au-dessus - 500 KMAD (Weber-Isover) / 1 MMAD (LPM).
     * Deux Transitions par cible (une par regroupement de Marque)
     * plutôt qu'une condition unique : le moteur ne supporte que des
     * conditions enchaînées AND/OR en séquence, pas de parenthésage -
     * "(Marque=X ET Montant<S1) OU (Marque=Y ET Montant<S2)" s'exprime
     * donc nativement avec deux lignes de Transition distinctes vers
     * la même Étape cible (BR-23 : la première qui correspond gagne).
     *
     * @param array<string, WorkflowStep> $steps
     */
    private function buildAccountTransitions(Workflow $workflow, array $steps, Form $form, array $marqueValues): void
    {
        $marqueField = $form->formFields()->where('technical_name', 'marque')->first();
        $montantField = $form->formFields()->where('technical_name', 'montant_concerne')->first();

        // DCM -> Crédit Client : toujours (pas de condition, l'avis DCM
        // "NON" est déjà couvert par le Rejet générique de la Validation).
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $steps['dcm']->id,
            'to_step_id' => $steps['credit_client']->id,
            'action_name' => 'valider_conditions_commerciales',
            'priority' => WorkflowPriority::Medium->value,
            'is_default' => true,
        ]);

        // Crédit Client -> DAF / DG, selon Marque + seuil.
        $thresholds = [
            // [marque, seuil, step cible]
            ['WEBER_ISOVER_LIKE' => ['WEBER_ISOVER'], 'seuil' => 500000, 'target' => 'daf', 'operator' => TransitionOperator::LessThan],
            ['WEBER_ISOVER_LIKE' => ['WEBER_ISOVER'], 'seuil' => 500000, 'target' => 'dg', 'operator' => TransitionOperator::GreaterThanOrEqual],
            ['WEBER_ISOVER_LIKE' => ['LPM'], 'seuil' => 1000000, 'target' => 'daf', 'operator' => TransitionOperator::LessThan],
            ['WEBER_ISOVER_LIKE' => ['LPM'], 'seuil' => 1000000, 'target' => 'dg', 'operator' => TransitionOperator::GreaterThanOrEqual],
        ];

        // Formulaire "Modification" : Weber et Isover sont deux options
        // distinctes (même seuil que Weber-Isover) - on démultiplie la
        // règle "WEBER_ISOVER_LIKE" sur toutes les valeurs de Marque
        // réellement présentes sur CE Formulaire qui appartiennent au
        // même regroupement de seuil.
        $weberIsoverGroup = array_values(array_intersect($marqueValues, ['WEBER_ISOVER', 'WEBER', 'ISOVER']));
        $lpmGroup = array_values(array_intersect($marqueValues, ['LPM']));

        $priority = WorkflowPriority::High->value;

        foreach ($thresholds as $rule) {
            $marqueGroup = $rule['WEBER_ISOVER_LIKE'] === ['WEBER_ISOVER'] ? $weberIsoverGroup : $lpmGroup;
            $targetStep = $steps[$rule['target']];

            foreach ($marqueGroup as $marqueValue) {
                $transition = WorkflowTransition::create([
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $steps['credit_client']->id,
                    'to_step_id' => $targetStep->id,
                    'action_name' => 'router_' . strtolower($rule['target']) . '_' . strtolower($marqueValue),
                    'priority' => $priority,
                    'is_default' => false,
                ]);

                if ($marqueField !== null) {
                    $transition->transitionConditions()->create([
                        'form_field_id' => $marqueField->id,
                        'operator' => TransitionOperator::Equals,
                        'expected_value' => $marqueValue,
                        'logical_operator' => TransitionLogicalOperator::And,
                        'execution_order' => 1,
                        'is_active' => true,
                    ]);
                }

                if ($montantField !== null) {
                    $transition->transitionConditions()->create([
                        'form_field_id' => $montantField->id,
                        'operator' => $rule['operator'],
                        'expected_value' => (string) $rule['seuil'],
                        'logical_operator' => TransitionLogicalOperator::And,
                        'execution_order' => 2,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // DAF et DG sont désormais eux-mêmes les Étapes de fin -
        // aucune Transition de clôture supplémentaire n'est nécessaire.
    }

    private function addOuvertureFields(User $admin, Form $form): void
    {
        $order = 1;
        $add = function (string $label, string $technicalName, string $type, bool $required = false, ?string $section = null) use ($form, &$order): FormField {
            return FormField::create([
                'form_id' => $form->id,
                'label' => $label,
                'section_title' => $section,
                'technical_name' => $technicalName,
                'field_type' => $type,
                'is_required' => $required,
                'display_order' => $order++,
            ]);
        };

        $add('Nom / Raison Sociale', 'nom_raison_sociale', 'text', true, section: 'Identité client');
        $add('N° ICE', 'n_ice', 'text', true);
        $add("Groupe d'appartenance", 'groupe_appartenance', 'text');
        $add('Siège social / Adresse', 'siege_social_adresse', 'textarea', true);
        $add('Objet social / Activité', 'objet_social_activite', 'text');
        $add('Capital social', 'capital_social', 'number');
        $add('Forme juridique', 'forme_juridique', 'text');
        $add('Date de création de la société', 'date_creation_societe', 'date');
        $add('Téléphone', 'telephone', 'text');
        $add('Email', 'email_client', 'email');
        $add('GSM', 'gsm', 'text');
        $add('Fax', 'fax', 'text');

        $marque = $add('Marque', 'marque', 'select', true, section: 'Marque et montant');
        FieldOption::create(['form_field_id' => $marque->id, 'value' => 'WEBER_ISOVER', 'label' => 'Weber - Isover', 'display_order' => 1, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $marque->id, 'value' => 'LPM', 'label' => 'LPM', 'display_order' => 2, 'created_by' => $admin->id]);

        $add('Montant du crédit client demandé (MAD)', 'montant_concerne', 'number', true);

        $add('Autres comptes déjà ouverts chez SGM', 'autres_comptes_sgm', 'textarea', section: 'Représentants');
        $add('Propriétaires / Associés (noms et fonctions)', 'proprietaires_associes', 'textarea');
        $add('Directeurs / Gérants (noms et fonctions)', 'directeurs_gerants', 'textarea');

        $add('Identifiant fiscal (IF)', 'identifiant_fiscal', 'text', section: 'Données comptables');
        $add('N° RC', 'n_rc', 'text');
        $add("Ville d'immatriculation", 'ville_immatriculation', 'text');
        $add('Patente', 'patente', 'text');
        $add('Banque(s) du client', 'banques_client', 'text');
        $add('Agence bancaire', 'agence_bancaire', 'text');
        $add('N° Compte (RIB)', 'n_compte_rib', 'text');

        $add('Extrait du Registre de commerce', 'piece_extrait_rc', 'file', true, section: 'Pièces jointes');
        $add('CGV cachetée et signée', 'piece_cgv', 'file', true);
        $add('Copie des statuts à jour', 'piece_statuts', 'file', true);
        $add('Modèle J', 'piece_modele_j', 'file', true);
        $add('Caution personnelle et solidaire', 'piece_caution', 'file', false);
        $add('Attestation de RIB / spécimen de chèque', 'piece_rib', 'file', true);
        $add('Copie CIN des dirigeants', 'piece_cin_dirigeants', 'file', true);
        $add('Copie des derniers états de synthèse', 'piece_etats_synthese', 'file', false);
        $add('Autres documents', 'piece_autres', 'file', false);
    }

    private function addModificationFields(User $admin, Form $form): void
    {
        $order = 1;
        $add = function (string $label, string $technicalName, string $type, bool $required = false, ?string $section = null) use ($form, &$order): FormField {
            return FormField::create([
                'form_id' => $form->id,
                'label' => $label,
                'section_title' => $section,
                'technical_name' => $technicalName,
                'field_type' => $type,
                'is_required' => $required,
                'display_order' => $order++,
            ]);
        };

        $add('Nom client', 'nom_client', 'text', true, section: 'Identité client');
        $add('Code client', 'code_client', 'text', true);
        $add('Responsable du compte', 'responsable_compte', 'text');
        $add('Activité', 'activite', 'text');

        $marque = $add('Marque', 'marque', 'select', true, section: 'Marque et nature de la demande');
        FieldOption::create(['form_field_id' => $marque->id, 'value' => 'LPM', 'label' => 'LPM', 'display_order' => 1, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $marque->id, 'value' => 'WEBER', 'label' => 'Weber', 'display_order' => 2, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $marque->id, 'value' => 'ISOVER', 'label' => 'Isover', 'display_order' => 3, 'created_by' => $admin->id]);

        $nature = $add('Nature de la demande', 'nature_demande', 'select', true);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'A', 'label' => "A - Modification plafond d'encours client", 'display_order' => 1, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'B', 'label' => 'B - Encours spécial', 'display_order' => 2, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'C', 'label' => 'C - Changement de délai et mode de paiement', 'display_order' => 3, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'D', 'label' => 'D - Réactivation du compte', 'display_order' => 4, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'E', 'label' => 'E - Changement affectation client sur système', 'display_order' => 5, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $nature->id, 'value' => 'F', 'label' => 'F - Autres demandes', 'display_order' => 6, 'created_by' => $admin->id]);

        $add('Montant concerné par la demande (MAD)', 'montant_concerne', 'number', true);

        $add("A - Seuil actuel d'encours", 'a_seuil_actuel', 'number', section: 'Détail de la demande (A à F)');
        $add('A - Seuil demandé', 'a_seuil_demande', 'number');
        $add('B - Encours spécial : seuil actuel', 'b_seuil_actuel', 'number');
        $add('B - Encours spécial : en-cours demandé', 'b_encours_demande', 'number');
        $add('B - Durée de validité', 'b_duree_validite', 'text');
        $add('C - Délai actuel', 'c_delai_actuel', 'text');
        $add('C - Délai demandé', 'c_delai_demande', 'text');
        $add('C - Mode de paiement actuel', 'c_mode_actuel', 'text');
        $add('C - Mode de paiement demandé', 'c_mode_demande', 'text');
        $add('D - Motif de réactivation', 'd_motif_reactivation', 'text');
        $add('E - Ancien DTC', 'e_ancien_dtc', 'text');
        $add('E - Nouveau DTC', 'e_nouveau_dtc', 'text');
        $add('F - Information complémentaire', 'f_information_complementaire', 'textarea');

        $add('Encours actuel', 'encours_actuel', 'number', section: 'Situation et garanties');
        $add("Nombre d'incidents durant l'année", 'nb_incidents_annee', 'number');
        $add('Montant garantie actuelle', 'montant_garantie_actuelle', 'number');

        $garantie = $add('Nouvelle garantie obtenue', 'nouvelle_garantie', 'select');
        FieldOption::create(['form_field_id' => $garantie->id, 'value' => 'ACMAR', 'label' => 'A - Couverture ACMAR', 'display_order' => 1, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $garantie->id, 'value' => 'CAUTION_PERSO', 'label' => 'B - Caution personnelle', 'display_order' => 2, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $garantie->id, 'value' => 'CHEQUE_AVANCE_PERSO', 'label' => "C - Chèque d'avance (personnel)", 'display_order' => 3, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $garantie->id, 'value' => 'CHEQUE_AVANCE_SOCIETE', 'label' => "D - Chèque d'avance (société)", 'display_order' => 4, 'created_by' => $admin->id]);
        FieldOption::create(['form_field_id' => $garantie->id, 'value' => 'LCN', 'label' => 'E - LCN', 'display_order' => 5, 'created_by' => $admin->id]);
    }
}
