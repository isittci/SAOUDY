<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLotRequest extends FormRequest
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
            'appel_offre_id' => 'required|exists:appels_offres,id_appel_offre',
            'numero' => [
                'required',
                'string',
                'max:20',
                // Unicité du numéro par appel d'offres
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\Lot::where('appel_offre_id', $this->appel_offre_id)
                        ->where('numero', $value)
                        ->whereNull('parent_id')
                        ->exists();

                    if ($exists) {
                        $fail('Ce numéro de lot existe déjà pour cet appel d\'offres.');
                    }
                },
            ],
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'statut_lot' => 'required|in:0,1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'appel_offre_id' => 'appel d\'offres',
            'numero' => 'numéro',
            'libelle' => 'libellé',
            'description_critere' => 'description',
            'specifications_techniques' => 'spécifications techniques',
            'date_debut_prevue' => 'date de début prévue',
            'date_fin_prevue' => 'date de fin prévue',
            'statut_lot' => 'statut',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'appel_offre_id.required' => 'L\'appel d\'offres est obligatoire.',
            'appel_offre_id.exists' => 'L\'appel d\'offres sélectionné est invalide.',
            'numero.required' => 'Le numéro du lot est obligatoire.',
            'numero.max' => 'Le numéro ne peut pas dépasser 20 caractères.',
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 160 caractères.',
            'date_fin_prevue.after' => 'La date de fin doit être postérieure à la date de début.',
            'statut_lot.required' => 'Le statut est obligatoire.',
            'statut_lot.in' => 'Le statut sélectionné est invalide.',
        ];
    }
}
