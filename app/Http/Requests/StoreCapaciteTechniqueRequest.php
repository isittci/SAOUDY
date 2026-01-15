<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCapaciteTechniqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'effectif_permanent_capacite_technique' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'effectif_temporaire_capacite_technique' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'moyens_materiels_capacite_technique' => ['nullable', 'string', 'max:5000'],
            'certifications_capacite_technique' => ['nullable', 'string', 'max:5000'],
            'agrements_capacite_technique' => ['nullable', 'string', 'max:5000'],
            'references_capacite_technique' => ['nullable', 'string', 'max:10'],
            'competences_cles_capacite_technique' => ['nullable', 'string', 'max:25'],
            'domaines_expertise_capacite_technique' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'effectif_permanent_capacite_technique' => 'effectif permanent',
            'effectif_temporaire_capacite_technique' => 'effectif temporaire',
            'moyens_materiels_capacite_technique' => 'moyens matériels',
            'certifications_capacite_technique' => 'certifications',
            'agrements_capacite_technique' => 'agréments',
            'references_capacite_technique' => 'références',
            'competences_cles_capacite_technique' => 'compétences clés',
            'domaines_expertise_capacite_technique' => 'domaines d\'expertise',
        ];
    }

    public function messages(): array
    {
        return [
            'effectif_permanent_capacite_technique.integer' => 'L\'effectif permanent doit être un nombre entier.',
            'effectif_permanent_capacite_technique.min' => 'L\'effectif permanent ne peut pas être négatif.',
            'effectif_temporaire_capacite_technique.integer' => 'L\'effectif temporaire doit être un nombre entier.',
            'effectif_temporaire_capacite_technique.min' => 'L\'effectif temporaire ne peut pas être négatif.',
            'references_capacite_technique.max' => 'Les références ne doivent pas dépasser :max caractères.',
            'competences_cles_capacite_technique.max' => 'Les compétences clés ne doivent pas dépasser :max caractères.',
        ];
    }
}
