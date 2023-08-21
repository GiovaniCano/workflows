<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_data(): void
    {
        $user = User::factory()->create();
        $previous_password = $user->password;

        $this->actingAs($user);

        $response = $this->put(route('user-profile-information.update'), [
            'name' => 'test name',
            'email' => 'test@email.com'
        ]);

        $user->refresh();

        $response->assertRedirect();
        $this->assertEquals($user->name, 'test name');
        $this->assertEquals($user->email, 'test@email.com');

        $response = $this->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'password_updated',
            'password_confirmation' => 'password_updated',
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertNotEquals($user->password, $previous_password);
    }

    public function test_user_can_delete_account(): void
    {
        $user = User::factory()->create();
        $workflow = $user->workflows()->create(['name' => 'workflow']);

        $this->actingAs($user);

        $response = $this->delete(route('user.destroy'));

        $response->assertRedirectToRoute('register');
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        $this->assertDatabaseMissing('sections', ['id' => $workflow->id]);
    }
}
