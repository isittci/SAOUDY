<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:permissions,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'resource' => ['required', 'string', 'max:100'],
            'action' => ['required', 'string', 'in:create,read,update,delete,export,import,validate,reject,restore,manage,download,duplicate'],
            'guard_name' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_system' => ['nullable', 'boolean'],
            'conditions' => ['nullable', 'json'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.max' => 'Le nom de la permission ne doit pas dépasser 100 caractères.',
            'slug.max' => 'Le slug ne doit pas dépasser 100 caractères.',
            'slug.unique' => 'Ce slug existe déjà.',
            'description.max' => 'La description ne doit pas dépasser 500 caractères.',
            'resource.required' => 'La ressource est obligatoire.',
            'resource.max' => 'La ressource ne doit pas dépasser 100 caractères.',
            'action.required' => 'L\'action est obligatoire.',
            'action.in' => 'L\'action sélectionnée n\'est pas valide.',
            'guard_name.max' => 'Le guard ne doit pas dépasser 50 caractères.',
            'category.max' => 'La catégorie ne doit pas dépasser 100 caractères.',
            'priority.integer' => 'La priorité doit être un nombre entier.',
            'priority.min' => 'La priorité doit être au minimum 0.',
            'priority.max' => 'La priorité ne doit pas dépasser 255.',
            'conditions.json' => 'Les conditions doivent être au format JSON valide.',
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
            'name' => 'nom',
            'slug' => 'slug',
            'description' => 'description',
            'resource' => 'ressource',
            'action' => 'action',
            'guard_name' => 'guard',
            'category' => 'catégorie',
            'priority' => 'priorité',
            'is_active' => 'actif',
            'is_system' => 'système',
            'conditions' => 'conditions',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);
        }

        if ($this->has('is_system')) {
            $this->merge([
                'is_system' => filter_var($this->is_system, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        if (!$this->has('guard_name') || empty($this->guard_name)) {
            $this->merge(['guard_name' => 'web']);
        }

        if (!$this->has('priority') || empty($this->priority)) {
            $this->merge(['priority' => 0]);
        }
    }
}
