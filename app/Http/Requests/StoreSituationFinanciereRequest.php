<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSituationFinanciereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercice_fiscal_situation_financiere' => ['required', 'string', 'max:36'],
            'chiffre_affaire_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:99999999999999999999'],
            'fonds_propres_situation_financiere' => ['nullable', 'numeric', 'max:99999999999999999999'],
            'capacite_emprunt_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:99999999999999999999'],
            'ratio_solvabilite_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'ratio_liquidite_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'resultat_net_situation_financiere' => ['nullable', 'numeric', 'max:99999999999999999999'],
            'total_actif_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:99999999999999999999'],
            'total_passif_situation_financiere' => ['nullable', 'numeric', 'min:0', 'max:99999999999999999999'],
            'observations_situation_financiere' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'exercice_fiscal_situation_financiere' => 'exercice fiscal',
            'chiffre_affaire_situation_financiere' => 'chiffre d\'affaires',
            'fonds_propres_situation_financiere' => 'fonds propres',
            'capacite_emprunt_situation_financiere' => 'capacité d\'emprunt',
            'ratio_solvabilite_situation_financiere' => 'ratio de solvabilité',
            'ratio_liquidite_situation_financiere' => 'ratio de liquidité',
            'resultat_net_situation_financiere' => 'résultat net',
            'total_actif_situation_financiere' => 'total actif',
            'total_passif_situation_financiere' => 'total passif',
            'observations_situation_financiere' => 'observations',
        ];
    }

    public function messages(): array
    {
        return [
            'exercice_fiscal_situation_financiere.required' => 'L\'exercice fiscal est obligatoire.',
            'chiffre_affaire_situation_financiere.numeric' => 'Le chiffre d\'affaires doit être un nombre.',
            'chiffre_affaire_situation_financiere.min' => 'Le chiffre d\'affaires ne peut pas être négatif.',
            'fonds_propres_situation_financiere.numeric' => 'Les fonds propres doivent être un nombre.',
            'ratio_solvabilite_situation_financiere.min' => 'Le ratio de solvabilité ne peut pas être négatif.',
            'ratio_solvabilite_situation_financiere.max' => 'Le ratio de solvabilité ne peut pas dépasser 1000%.',
            'ratio_liquidite_situation_financiere.min' => 'Le ratio de liquidité ne peut pas être négatif.',
            'resultat_net_situation_financiere.numeric' => 'Le résultat net doit être un nombre.',
            'total_actif_situation_financiere.min' => 'Le total actif ne peut pas être négatif.',
            'total_passif_situation_financiere.min' => 'Le total passif ne peut pas être négatif.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Nettoyer les montants (supprimer les espaces)
        $fields = [
            'chiffre_affaire_situation_financiere',
            'fonds_propres_situation_financiere',
            'capacite_emprunt_situation_financiere',
            'resultat_net_situation_financiere',
            'total_actif_situation_financiere',
            'total_passif_situation_financiere',
        ];

        $cleaned = [];
        foreach ($fields as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                if (is_string($value)) {
                    $cleaned[$field] = str_replace([' ', ' '], '', $value);
                }
            }
        }

        if (!empty($cleaned)) {
            $this->merge($cleaned);
        }
    }
}
