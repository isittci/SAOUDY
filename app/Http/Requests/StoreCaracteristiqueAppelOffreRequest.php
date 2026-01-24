<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaracteristiqueAppelOffreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date_demarrage_prevue_caracteristique_appel_offre' => 'nullable|date|after_or_equal:today',
            'duree_estimee_jours_caracteristique_appel_offre' => 'nullable|integer|min:1|max:3650',
            'date_livraison_previsionnelle_caracteristique_appel_offre' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($this->date_demarrage_prevue_caracteristique_appel_offre && $value) {
                        $debut = new \DateTime($this->date_demarrage_prevue_caracteristique_appel_offre);
                        $fin = new \DateTime($value);

                        if ($fin <= $debut) {
                            $fail('La date de livraison doit être postérieure à la date de démarrage.');
                        }
                    }
                },
            ],
            'lieu_execution_caracteristique_appel_offre' => 'nullable|string|max:255',
            'montant_garantie_caracteristique_appel_offre' => 'nullable|numeric|min:0|max:999999999999.99',
            'delai_garantie_jours_caracteristique_appel_offre' => 'nullable|integer|min:0|max:3650',
            'conditions_paiement_caracteristique_appel_offre' => 'nullable|string|max:5000',
            'modalites_execution_caracteristique_appel_offre' => 'nullable|string|max:5000',
            'documents_requis_caracteristique_appel_offre' => 'nullable|string|max:5000',
            'autres_informations_caracteristique_appel_offre' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'date_demarrage_prevue_caracteristique_appel_offre' => 'date de démarrage prévue',
            'duree_estimee_jours_caracteristique_appel_offre' => 'durée estimée (jours)',
            'date_livraison_previsionnelle_caracteristique_appel_offre' => 'date de livraison prévisionnelle',
            'lieu_execution_caracteristique_appel_offre' => 'lieu d\'exécution',
            'montant_garantie_caracteristique_appel_offre' => 'montant de garantie',
            'delai_garantie_jours_caracteristique_appel_offre' => 'délai de garantie (jours)',
            'conditions_paiement_caracteristique_appel_offre' => 'conditions de paiement',
            'modalites_execution_caracteristique_appel_offre' => 'modalités d\'exécution',
            'documents_requis_caracteristique_appel_offre' => 'documents requis',
            'autres_informations_caracteristique_appel_offre' => 'autres informations',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'date_demarrage_prevue_caracteristique_appel_offre.after_or_equal' => 'La date de démarrage ne peut pas être dans le passé.',
            'duree_estimee_jours_caracteristique_appel_offre.min' => 'La durée doit être d\'au moins 1 jour.',
            'duree_estimee_jours_caracteristique_appel_offre.max' => 'La durée ne peut pas dépasser 10 ans.',
            'montant_garantie_caracteristique_appel_offre.numeric' => 'Le montant de garantie doit être un nombre.',
            'delai_garantie_jours_caracteristique_appel_offre.max' => 'Le délai de garantie ne peut pas dépasser 10 ans.',
        ];
    }
}

