<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_register_view_is_accessible()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSeeInOrder([
            'name="_token"',
            'name="email"',
            'name="name"',
            'name="password"',
            'name="password_confirmation"',
            'name="terms"',
            'type="submit"',
        ], false);
        
        $response->assertSee([
            route('terms'),
            route('privacy'),
        ], false);
    }

    public function test_user_can_register_with_valid_data()
    {
        $name = $this->faker()->name();
        $email = $this->faker()->safeEmail();

        $response = $this->post(route('register'), [
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);
        $response->assertRedirectToRoute('workflow.index');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function test_user_cant_register_with_invalid_data()
    {
        $response = $this->post('register', [
            'name' => '',
            'email' => 'nope',
            'password' => 'nope',
            'password_confirmation' => '',
            'terms' => false,
        ]);

        $response->assertInvalid(['name', 'email', 'password', 'terms']);
        $response->assertStatus(302);

        $this->assertGuest();
    }

    /* LOGIN */

    public function test_login_view_is_accessible()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSeeInOrder([
            'name="_token"',
            'name="email"',
            'name="password"',
            'name="remember"',
            'type="submit"',
        ], false);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('workflow.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {

        $response = $this->post(route('login'), [
            'email' => 'invalid@example.com',
            'password' => 'invalid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $response->assertSessionDoesntHaveErrors('password');
        $this->assertGuest();

        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'invalid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $response->assertSessionDoesntHaveErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('workflow.index'));
        $response->assertSeeInOrder([
            route('logout'),
            'type="submit"',
            'value="'.__('Logout').'"'
        ], false);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('landing-page'));
        $this->assertGuest();
    }
}
