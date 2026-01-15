<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Extensions de fichiers autorisées
     */
    const EXTENSIONS_AUTORISEES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];

    /**
     * Taille maximale en Ko (10 Mo)
     */
    const TAILLE_MAX_KO = 10240;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $extensionsString = implode(',', self::EXTENSIONS_AUTORISEES);

        return [
            'type_document' => [
                'required',
                'string',
                'max:20',
            ],
            'titre_document' => [
                'required',
                'string',
                'max:100',
            ],
            'fichier' => [
                'required',
                'file',
                'max:' . self::TAILLE_MAX_KO,
                'mimes:' . $extensionsString,
            ],
            'description_document' => [
                'nullable',
                'string',
                'max:120',
            ],
            'date_document' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type_document' => 'type de document',
            'titre_document' => 'titre du document',
            'fichier' => 'fichier',
            'description_document' => 'description',
            'date_document' => 'date du document',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $tailleMaxMo = self::TAILLE_MAX_KO / 1024;
        $extensionsListe = implode(', ', self::EXTENSIONS_AUTORISEES);

        return [
            'type_document.required' => 'Le type de document est obligatoire.',
            'type_document.max' => 'Le type de document ne doit pas dépasser :max caractères.',

            'titre_document.required' => 'Le titre du document est obligatoire.',
            'titre_document.max' => 'Le titre ne doit pas dépasser :max caractères.',

            'fichier.required' => 'Veuillez sélectionner un fichier à uploader.',
            'fichier.file' => 'Le fichier uploadé n\'est pas valide.',
            'fichier.max' => "Le fichier ne doit pas dépasser {$tailleMaxMo} Mo.",
            'fichier.mimes' => "Les formats acceptés sont: {$extensionsListe}.",

            'description_document.max' => 'La description ne doit pas dépasser :max caractères.',

            'date_document.date' => 'La date du document n\'est pas valide.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer les données
        if ($this->has('titre_document')) {
            $this->merge([
                'titre_document' => trim($this->titre_document),
            ]);
        }

        if ($this->has('description_document')) {
            $this->merge([
                'description_document' => trim($this->description_document),
            ]);
        }
    }
}
