<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté.
     */
    public function index()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $user = $user->load(['role.permissions', 'creator']);

        return view('profile.index', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil.
     */
    public function update(Request $request)
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();

            $validated = $request->validate([
                'nom_complet' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'telephone_principal' => 'nullable|string|max:20',
                'telephone_secondaire' => 'nullable|string|max:20',
            ], [
                'nom_complet.required' => 'Le nom complet est obligatoire.',
                'nom_complet.max' => 'Le nom ne doit pas dépasser 100 caractères.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'L\'adresse email n\'est pas valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'telephone_principal.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
                'telephone_secondaire.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            ]);

            $user->update([
                'nom_complet' => $validated['nom_complet'],
                'email' => $validated['email'],
                'telephone_principal' => $validated['telephone_principal'],
                'telephone_secondaire' => $validated['telephone_secondaire'],
                'updated_by' => $user->id,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil mis à jour avec succès.',
                    'data' => $user->fresh(),
                ]);
            }

            return back()->with('success', 'Profil mis à jour avec succès.');

        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du profil.',
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du profil.');
        }
    }

    /**
     * Mettre à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            ], [
                'current_password.required' => 'Le mot de passe actuel est obligatoire.',
                'password.required' => 'Le nouveau mot de passe est obligatoire.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            ]);

            // Vérifier le mot de passe actuel
            if (!Hash::check($validated['current_password'], $user->password)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le mot de passe actuel est incorrect.',
                        'errors' => ['current_password' => ['Le mot de passe actuel est incorrect.']],
                    ], 422);
                }

                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }

            $user->update([
                'password' => Hash::make($validated['password']),
                'updated_by' => $user->id,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe mis à jour avec succès.',
                ]);
            }

            return back()->with('success', 'Mot de passe mis à jour avec succès.');

        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du mot de passe: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du mot de passe.',
                ], 500);
            }

            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du mot de passe.');
        }
    }
}
