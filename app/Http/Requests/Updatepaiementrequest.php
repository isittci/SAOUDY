<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Paiement;

class UpdatePaiementRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Vous pouvez ajouter ici une logique d'autorisation spécifique
        return true;
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facture_id' => [
                'sometimes',
                'required',
                'uuid',
                'exists:factures,id_facture',
            ],
            'banque_id' => [
                'sometimes',
                'required',
                'uuid',
                'exists:banques,id_banque',
            ],
            'montant_net_paye_paiement' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:99999999999999999.99',
            ],
            'statut_paiement' => [
                'nullable',
                'integer',
                'in:' . implode(',', array_keys(Paiement::getStatuts())),
            ],
            'date_validation_paiement' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'date_effectif_paiement' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'observations_paiement' => [
                'nullable',
                'string',
                'max:65535',
            ],
            'motif_rejet_paiement' => [
                'nullable',
                'string',
                'max:65535',
            ],
            'valide_par' => [
                'nullable',
                'uuid',
                'exists:users,id',
            ],
            'paye_par' => [
                'nullable',
                'uuid',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Obtenir les messages de validation personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facture_id.required' => 'La facture est obligatoire.',
            'facture_id.uuid' => 'L\'identifiant de la facture est invalide.',
            'facture_id.exists' => 'La facture sélectionnée n\'existe pas.',

            'banque_id.required' => 'La banque est obligatoire.',
            'banque_id.uuid' => 'L\'identifiant de la banque est invalide.',
            'banque_id.exists' => 'La banque sélectionnée n\'existe pas.',

            'montant_net_paye_paiement.required' => 'Le montant du paiement est obligatoire.',
            'montant_net_paye_paiement.numeric' => 'Le montant doit être un nombre valide.',
            'montant_net_paye_paiement.min' => 'Le montant doit être supérieur à 0.',
            'montant_net_paye_paiement.max' => 'Le montant est trop élevé.',

            'statut_paiement.integer' => 'Le statut doit être un nombre entier.',
            'statut_paiement.in' => 'Le statut sélectionné est invalide.',

            'date_validation_paiement.date' => 'La date de validation doit être une date valide.',
            'date_validation_paiement.before_or_equal' => 'La date de validation ne peut pas être dans le futur.',

            'date_effectif_paiement.date' => 'La date effective doit être une date valide.',
            'date_effectif_paiement.before_or_equal' => 'La date effective ne peut pas être dans le futur.',

            'observations_paiement.string' => 'Les observations doivent être du texte.',
            'observations_paiement.max' => 'Les observations sont trop longues.',

            'motif_rejet_paiement.string' => 'Le motif de rejet doit être du texte.',
            'motif_rejet_paiement.max' => 'Le motif de rejet est trop long.',

            'valide_par.uuid' => 'L\'identifiant du validateur est invalide.',
            'valide_par.exists' => 'Le validateur sélectionné n\'existe pas.',

            'paye_par.uuid' => 'L\'identifiant du payeur est invalide.',
            'paye_par.exists' => 'Le payeur sélectionné n\'existe pas.',
        ];
    }

    /**
     * Obtenir les attributs personnalisés pour les erreurs de validation.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'facture_id' => 'facture',
            'banque_id' => 'banque',
            'montant_net_paye_paiement' => 'montant du paiement',
            'statut_paiement' => 'statut',
            'date_validation_paiement' => 'date de validation',
            'date_effectif_paiement' => 'date effective',
            'observations_paiement' => 'observations',
            'motif_rejet_paiement' => 'motif de rejet',
            'valide_par' => 'validateur',
            'paye_par' => 'payeur',
        ];
    }

    /**
     * Préparer les données pour la validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le montant (enlever les espaces et autres caractères)
        if ($this->has('montant_net_paye_paiement')) {
            $montant = $this->montant_net_paye_paiement;

            // Enlever les espaces et les caractères de séparation de milliers
            $montant = str_replace([' ', ','], ['', '.'], $montant);

            $this->merge([
                'montant_net_paye_paiement' => $montant,
            ]);
        }
    }

    /**
     * Configurer le validateur après sa création.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validation personnalisée: si le statut est REJETE, le motif de rejet est obligatoire
            if ($this->has('statut_paiement') &&
                $this->statut_paiement == Paiement::STATUT_REJETE &&
                empty($this->motif_rejet_paiement)) {
                $validator->errors()->add(
                    'motif_rejet_paiement',
                    'Le motif de rejet est obligatoire pour un paiement rejeté.'
                );
            }

            // Validation personnalisée: si le statut est VALIDE, la date de validation est obligatoire
            if ($this->has('statut_paiement') &&
                $this->statut_paiement == Paiement::STATUT_VALIDE &&
                empty($this->date_validation_paiement)) {
                $validator->errors()->add(
                    'date_validation_paiement',
                    'La date de validation est obligatoire pour un paiement validé.'
                );
            }

            // Validation personnalisée: si le statut est PAYE, le payeur est obligatoire
            if ($this->has('statut_paiement') &&
                $this->statut_paiement == Paiement::STATUT_PAYE &&
                empty($this->paye_par)) {
                $validator->errors()->add(
                    'paye_par',
                    'Le payeur est obligatoire pour un paiement effectué.'
                );
            }

            // Vérifier que le paiement peut encore être modifié
            $paiement = $this->route('paiement');
            if ($paiement && !$paiement->peutEtreModifie()) {
                $validator->errors()->add(
                    'statut_paiement',
                    'Ce paiement ne peut plus être modifié (statut: ' . $paiement->statut_libelle . ').'
                );
            }
        });
    }
}
