<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');
        return auth()->check() && 
               auth()->user()->hasPermission('users.update') &&
               auth()->user()->canManageUser($user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'nom_complet' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId), 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'telephone_principal' => ['nullable', 'string', 'max:20'],
            'telephone_secondaire' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'uuid', 'exists:roles,id'],
            'statut' => ['required', 'in:0,1'],
            'email_verified' => ['nullable', 'boolean'],
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
            'nom_complet' => 'nom complet',
            'email' => 'adresse email',
            'password' => 'mot de passe',
            'telephone_principal' => 'téléphone principal',
            'telephone_secondaire' => 'téléphone secondaire',
            'role_id' => 'rôle',
            'statut' => 'statut',
            'email_verified' => 'email vérifié',
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
            'nom_complet.required' => 'Le nom complet est obligatoire.',
            'nom_complet.max' => 'Le nom complet ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être Actif ou Inactif.',
        ];
    }
}
