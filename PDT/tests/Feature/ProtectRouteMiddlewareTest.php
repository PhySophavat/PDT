<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProtectRouteMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'protect.route'])->get('/middleware-check', function () {
            return response('ok');
        });
    }

    public function test_guest_is_redirected_from_a_protected_route(): void
    {
        $response = $this->get('/middleware-check');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('url.intended', url('/middleware-check'));
    }

    public function test_authenticated_user_can_access_a_protected_route(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $response = $this->actingAs($user)->get('/middleware-check');

        $response->assertOk();
        $response->assertSee('ok');
    }
}
