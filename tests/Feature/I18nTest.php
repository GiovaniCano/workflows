<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class I18nTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_set_locale_with_authentication()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('set-locale'), ['locale' => 'es']);

        $response->assertRedirect();
        $this->assertEquals('es', $user->fresh()->locale);
    }

    public function test_user_can_set_locale_without_authentication()
    {
        $response = $this->post(route('set-locale'), ['locale' => 'es']);

        $response->assertRedirect();
        $response->assertCookie('locale', 'es', false);
    }

    public function test_locale_middleware_sets_locale_with_authentication()
    {
        $user = User::factory()->create(['locale' => 'es']);
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals('es', app()->getLocale());
        $response->assertCookieMissing('locale');
    }

    public function test_locale_middleware_sets_locale_with_cookie()
    {
        $response = $this->withUnencryptedCookie('locale', 'es')->get('/');
        $response->assertStatus(200);
        $this->assertEquals('es', app()->getLocale());
    }

    public function test_locale_middleware_sets_locale_with_browser_language()
    {
        $browserLanguage = 'es_MX';
        $response = $this->get('/', ['Accept-Language' => $browserLanguage]);

        $response->assertStatus(200);
        $response->assertCookieMissing('locale');
        $this->assertEquals('es', app()->getLocale());
    }
}
