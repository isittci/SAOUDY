<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBanqueRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom_banque' => [
                'required',
                'string',
                'max:150',
            ],

            'code_banque' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^[A-Za-z0-9]+$/',
            ],

            'numero_compte_banque' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^[A-Za-z0-9]+$/',
            ],

            'code_guichet_banque' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^[A-Za-z0-9]+$/',
            ],

            'cle_rib_banque' => [
                'nullable',
                'string',
                'max:25',
                'regex:/^[A-Za-z0-9]+$/',
            ],

            'iban_banque' => [
                'nullable',
                'string',
                'max:34',
                'regex:/^[A-Z]{2}[0-9]{2}[A-Za-z0-9]{1,30}$/',
            ],

            'swift_bic_banque' => [
                'nullable',
                'string',
                'max:11',
                'regex:/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/',
            ],

            'titulaire_compte_banque' => [
                'nullable',
                'string',
                'max:50',
            ],

            'actif_banque' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom_banque.required' => 'Le nom de la banque est obligatoire.',
            'nom_banque.string' => 'Le nom de la banque doit être une chaîne de caractères.',
            'nom_banque.max' => 'Le nom de la banque ne peut pas dépasser :max caractères.',

            'code_banque.string' => 'Le code banque doit être une chaîne de caractères.',
            'code_banque.max' => 'Le code banque ne peut pas dépasser :max caractères.',
            'code_banque.regex' => 'Le code banque ne doit contenir que des lettres et des chiffres.',

            'numero_compte_banque.string' => 'Le numéro de compte doit être une chaîne de caractères.',
            'numero_compte_banque.max' => 'Le numéro de compte ne peut pas dépasser :max caractères.',
            'numero_compte_banque.regex' => 'Le numéro de compte ne doit contenir que des lettres et des chiffres.',

            'code_guichet_banque.string' => 'Le code guichet doit être une chaîne de caractères.',
            'code_guichet_banque.max' => 'Le code guichet ne peut pas dépasser :max caractères.',
            'code_guichet_banque.regex' => 'Le code guichet ne doit contenir que des lettres et des chiffres.',

            'cle_rib_banque.string' => 'La clé RIB doit être une chaîne de caractères.',
            'cle_rib_banque.max' => 'La clé RIB ne peut pas dépasser :max caractères.',
            'cle_rib_banque.regex' => 'La clé RIB ne doit contenir que des lettres et des chiffres.',

            'iban_banque.string' => 'L\'IBAN doit être une chaîne de caractères.',
            'iban_banque.max' => 'L\'IBAN ne peut pas dépasser :max caractères.',
            'iban_banque.regex' => 'Le format de l\'IBAN n\'est pas valide.',

            'swift_bic_banque.string' => 'Le code SWIFT/BIC doit être une chaîne de caractères.',
            'swift_bic_banque.max' => 'Le code SWIFT/BIC ne peut pas dépasser :max caractères.',
            'swift_bic_banque.regex' => 'Le format du code SWIFT/BIC n\'est pas valide.',

            'titulaire_compte_banque.string' => 'Le nom du titulaire doit être une chaîne de caractères.',
            'titulaire_compte_banque.max' => 'Le nom du titulaire ne peut pas dépasser :max caractères.',

            'actif_banque.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }

    /**
     * Attributs personnalisés pour les messages d'erreur.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom_banque' => 'nom de la banque',
            'code_banque' => 'code banque',
            'numero_compte_banque' => 'numéro de compte',
            'code_guichet_banque' => 'code guichet',
            'cle_rib_banque' => 'clé RIB',
            'iban_banque' => 'IBAN',
            'swift_bic_banque' => 'SWIFT/BIC',
            'titulaire_compte_banque' => 'titulaire du compte',
            'actif_banque' => 'statut actif',
        ];
    }

    /**
     * Préparer les données pour la validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer l'IBAN (retirer les espaces)
        if ($this->has('iban_banque') && $this->iban_banque) {
            $this->merge([
                'iban_banque' => strtoupper(str_replace(' ', '', $this->iban_banque)),
            ]);
        }

        // Mettre en majuscules le SWIFT/BIC
        if ($this->has('swift_bic_banque') && $this->swift_bic_banque) {
            $this->merge([
                'swift_bic_banque' => strtoupper(trim($this->swift_bic_banque)),
            ]);
        }

        // Mettre en majuscules le code banque
        if ($this->has('code_banque') && $this->code_banque) {
            $this->merge([
                'code_banque' => strtoupper(trim($this->code_banque)),
            ]);
        }

        // Mettre en majuscules le code guichet
        if ($this->has('code_guichet_banque') && $this->code_guichet_banque) {
            $this->merge([
                'code_guichet_banque' => strtoupper(trim($this->code_guichet_banque)),
            ]);
        }
    }

    /**
     * Gérer les erreurs de validation pour les requêtes AJAX.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation.',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
