<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginWithEmailOrPhoneTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur de test
        $this->user = User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'nom_complet' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'telephone_principal' => '+225 07 12 34 56 78',
            'telephone_secondaire' => '+225 05 98 76 54 32',
            'role_id' => \App\Models\Role::first()->id ?? null,
            'statut' => 1,
        ]);
    }

    /** @test */
    public function user_can_login_with_email()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $this->assertNotNull(cache()->get('verification_code:' . $this->user->password_reset_token));
    }

    /** @test */
    public function user_can_login_with_principal_phone()
    {
        $response = $this->post(route('auth.login'), [
            'email' => '+225 07 12 34 56 78',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /** @test */
    public function user_can_login_with_secondary_phone()
    {
        $response = $this->post(route('auth.login'), [
            'email' => '+225 05 98 76 54 32',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /** @test */
    public function login_fails_with_wrong_email()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_fails_with_wrong_phone()
    {
        $response = $this->post(route('auth.login'), [
            'email' => '+225 01 11 11 11 11',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_fails_with_wrong_password()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_fails_with_inactive_user()
    {
        $this->user->update(['statut' => 0]);

        $response = $this->post(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_validation_fails_without_email()
    {
        $response = $this->post(route('auth.login'), [
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_validation_fails_without_password()
    {
        $response = $this->post(route('auth.login'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function api_login_returns_json_response()
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'token',
            'expires_in',
        ]);
    }

    /** @test */
    public function api_login_fails_with_wrong_credentials()
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Identifiants incorrects.',
        ]);
    }
}
