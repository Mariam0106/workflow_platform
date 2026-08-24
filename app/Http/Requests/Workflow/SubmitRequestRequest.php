<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow;

use App\DataTransferObjects\Workflow\SubmitRequestData;
use App\Enums\FormPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * SubmitRequestRequest
 * ==========================================================================
 *
 * Validation structurelle minimale (BR-56 : la validation fine par champ
 * de formulaire dynamique - obligatoire/type/regles - releve du
 * DynamicFormRuleBuilder, Etape "Formulaires dynamiques", pas d'ici).
 * Convertit l'input HTTP valide en SubmitRequestData (Etape 6) - le
 * Controller ne voit jamais un tableau brut.
 *
 * Champs de type "file" (BR-51) : un champ dynamique peut être configuré
 * en type "file" - il arrive donc ici comme un vrai fichier téléversé
 * dans values[{field_id}], pas une chaîne. SubmitRequestData/RequestValue
 * n'acceptent qu'une valeur texte, et aucune Request n'existe encore à
 * ce stade pour y rattacher une Pièce Jointe (elle est créée PAR
 * submit() lui-même) - prepareForValidation() extrait donc chaque
 * fichier avant que la validation ne s'exécute, le remplace par son nom
 * original (valeur texte lisible pour "Informations soumises"), et le
 * garde de côté via fieldFiles() pour que le Controller le stocke comme
 * Pièce Jointe UNE FOIS la Request créée.
 * ==========================================================================
 */
class SubmitRequestRequest extends FormRequest
{
    /** @var array<int, UploadedFile> [form_field_id => fichier] */
    private array $extractedFieldFiles = [];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $values = (array) $this->input('values', []);

        foreach ((array) $this->file('values', []) as $fieldId => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $this->extractedFieldFiles[$fieldId] = $file;
                $values[$fieldId] = $file->getClientOriginalName();
            }
        }

        $this->merge(['values' => $values]);
    }

    /**
     * Sans ce remplacement, Laravel valide par défaut contre
     * $this->all() = input() fusionné avec files() - et cette fusion
     * RÉINJECTE le fichier brut (UploadedFile) par-dessus la chaîne
     * de remplacement posée par prepareForValidation() ci-dessus, pour
     * n'importe quel Champ "file" réellement rempli. D'où l'erreur
     * "must be a string" alors que la valeur avait déjà été
     * correctement transformée en texte : ce n'était pas la valeur
     * corrigée qui était validée, mais le fichier brut ramené par
     * cette fusion automatique.
     *
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return $this->input();
    }

    /**
     * @return array<int, UploadedFile> [form_field_id => fichier]
     */
    public function fieldFiles(): array
    {
        return $this->extractedFieldFiles;
    }

    /**
     * Règles construites dynamiquement à partir des Champs réels du
     * Formulaire (chaque Champ garde son propre "obligatoire" - un
     * Champ facultatif ne doit jamais bloquer l'envoi) plutôt qu'un
     * "values.*" => required global qui les traitait tous pareil, quel
     * que soit leur propre is_required.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'priority' => ['nullable', Rule::enum(FormPriority::class)],
            'values' => ['array'],
        ];

        $form = $this->route('form');

        if ($form !== null) {
            foreach ($form->formFields as $field) {
                $rules["values.{$field->id}"] = [
                    $field->is_required ? 'required' : 'nullable',
                    'string',
                    'max:5000',
                ];
            }
        }

        return $rules;
    }

    /**
     * Noms de champs lisibles pour les messages d'erreur - sans ça,
     * Laravel affiche la clé technique brute ("values.31").
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $form = $this->route('form');

        if ($form === null) {
            return [];
        }

        return $form->formFields
            ->mapWithKeys(fn ($field) => ["values.{$field->id}" => $field->label])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'values.*.required' => 'Le champ « :attribute » est obligatoire.',
            'values.*.string' => 'Le champ « :attribute » est invalide.',
            'values.*.max' => 'Le champ « :attribute » ne doit pas dépasser :max caractères.',
            'form_id.required' => 'Formulaire manquant.',
            'form_id.exists' => 'Ce formulaire est introuvable.',
        ];
    }

    public function toDto(): SubmitRequestData
    {
        return SubmitRequestData::fromArray([
            'form_id' => $this->validated('form_id'),
            'requester_id' => $this->user()->id,
            'values' => $this->validated('values'),
            'priority' => $this->validated('priority') ?? FormPriority::Normal->value,
        ]);
    }
}
