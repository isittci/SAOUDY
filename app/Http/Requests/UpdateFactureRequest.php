<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateFactureRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une facture.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $factureId = $this->route('facture');

        return [
            'proforma_id' => [
                'required',
                'uuid',
                'exists:proformas,id_proforma',
                Rule::unique('factures', 'proforma_id')
                    ->ignore($factureId, 'id_facture')
                    ->whereNull('deleted_at'),
            ],
            'numero_facture' => [
                'required',
                'string',
                'max:30',
                Rule::unique('factures', 'numero_facture')
                    ->ignore($factureId, 'id_facture')
                    ->whereNull('deleted_at'),
            ],
            'montant_facture' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],
            'date_facture' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'date_reception_facture' => [
                'required',
                'date',
                'after_or_equal:date_facture',
                'before_or_equal:today',
            ],
            'comment_facture' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /**
     * Messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'proforma_id.required' => 'La proforma est obligatoire.',
            'proforma_id.uuid' => 'L\'identifiant de la proforma n\'est pas valide.',
            'proforma_id.exists' => 'La proforma sélectionnée n\'existe pas.',
            'proforma_id.unique' => 'Une facture existe déjà pour cette proforma.',

            'numero_facture.required' => 'Le numéro de facture est obligatoire.',
            'numero_facture.string' => 'Le numéro de facture doit être une chaîne de caractères.',
            'numero_facture.max' => 'Le numéro de facture ne peut pas dépasser 30 caractères.',
            'numero_facture.unique' => 'Ce numéro de facture existe déjà.',

            'montant_facture.required' => 'Le montant de la facture est obligatoire.',
            'montant_facture.numeric' => 'Le montant de la facture doit être un nombre.',
            'montant_facture.min' => 'Le montant de la facture ne peut pas être négatif.',
            'montant_facture.max' => 'Le montant de la facture dépasse la limite autorisée.',

            'date_facture.required' => 'La date de la facture est obligatoire.',
            'date_facture.date' => 'La date de la facture n\'est pas valide.',
            'date_facture.before_or_equal' => 'La date de la facture ne peut pas être dans le futur.',

            'date_reception_facture.required' => 'La date de réception est obligatoire.',
            'date_reception_facture.date' => 'La date de réception n\'est pas valide.',
            'date_reception_facture.after_or_equal' => 'La date de réception doit être postérieure ou égale à la date de la facture.',
            'date_reception_facture.before_or_equal' => 'La date de réception ne peut pas être dans le futur.',

            'comment_facture.string' => 'Le commentaire doit être une chaîne de caractères.',
            'comment_facture.max' => 'Le commentaire ne peut pas dépasser 5000 caractères.',
        ];
    }

    /**
     * Noms des attributs pour les messages d'erreur.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'proforma_id' => 'proforma',
            'numero_facture' => 'numéro de facture',
            'montant_facture' => 'montant',
            'date_facture' => 'date de facture',
            'date_reception_facture' => 'date de réception',
            'comment_facture' => 'commentaire',
        ];
    }

    /**
     * Préparer les données pour la validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le montant (supprimer les espaces et remplacer la virgule par un point)
        if ($this->has('montant_facture') && is_string($this->montant_facture)) {
            $montant = str_replace([' ', ' '], '', $this->montant_facture);
            $montant = str_replace(',', '.', $montant);
            $this->merge(['montant_facture' => $montant]);
        }
    }
}
