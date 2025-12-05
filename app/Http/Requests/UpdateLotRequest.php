<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class UpdateLotRequest extends FormRequest
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
            'libelle' => 'required|string|max:160',
            'description_critere' => 'nullable|string',
            'specifications_techniques' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut_prevue',
            'taux_penalites' => 'nullable|numeric|min:0|max:100',
            'statut_lot' => 'required|in:0,1',
            'motif_modification' => 'required|string|max:500',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'libelle' => 'libellé',
            'description_critere' => 'description',
            'specifications_techniques' => 'spécifications techniques',
            'date_debut_prevue' => 'date de début prévue',
            'date_fin_prevue' => 'date de fin prévue',
            'taux_penalites' => 'taux de pénalités',
            'statut_lot' => 'statut',
            'motif_modification' => 'motif de modification',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 160 caractères.',
            'date_fin_prevue.after' => 'La date de fin doit être postérieure à la date de début.',
            'taux_penalites.numeric' => 'Le taux de pénalités doit être un nombre.',
            'taux_penalites.min' => 'Le taux de pénalités ne peut pas être négatif.',
            'taux_penalites.max' => 'Le taux de pénalités ne peut pas dépasser 100%.',
            'statut_lot.required' => 'Le statut est obligatoire.',
            'statut_lot.in' => 'Le statut sélectionné est invalide.',
            'motif_modification.required' => 'Le motif de modification est obligatoire.',
            'motif_modification.max' => 'Le motif ne peut pas dépasser 500 caractères.',
        ];
    }
}
