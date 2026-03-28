<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DashboardAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])->get('/admin-only-check', function () {
            return response('admin-ok');
        });
    }

    public function test_admin_can_access_admin_only_route(): void
    {
        $admin = new class([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'secret',
        ]) extends User {
            public function hasRole($role)
            {
                return $role === 'admin';
            }
        };

        $response = $this->actingAs($admin)->get('/admin-only-check');

        $response->assertOk();
        $response->assertSee('admin-ok');
    }

    public function test_teacher_is_forbidden_from_admin_only_route(): void
    {
        $teacher = new class([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => 'secret',
        ]) extends User {
            public function hasRole($role)
            {
                return $role === 'teacher';
            }
        };

        $response = $this->actingAs($teacher)->get('/admin-only-check');

        $response->assertForbidden();
    }

    public function test_teacher_redirect_logic_skips_dashboard(): void
    {
        $teacher = new class([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => 'secret',
        ]) extends User {
            public function hasRole($role)
            {
                return $role === 'teacher';
            }
        };

        $controller = new class extends AuthController {
            public function exposedShouldForgetIntendedUrl(User $user, ?string $intendedUrl): bool
            {
                return $this->shouldForgetIntendedUrl($user, $intendedUrl);
            }

            public function exposedDefaultRedirectPath(User $user): string
            {
                return $this->defaultRedirectPath($user);
            }
        };

        $this->assertTrue($controller->exposedShouldForgetIntendedUrl($teacher, route('dashboard')));
        $this->assertSame(route('class.index'), $controller->exposedDefaultRedirectPath($teacher));
    }

    public function test_direct_permission_redirect_falls_back_to_capability_routes(): void
    {
        $manager = new class([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => 'secret',
        ]) extends User {
            public function hasRole($role)
            {
                return false;
            }

            public function canManageTeachers(): bool
            {
                return true;
            }
        };

        $controller = new class extends AuthController {
            public function exposedDefaultRedirectPath(User $user): string
            {
                return $this->defaultRedirectPath($user);
            }
        };

        $this->assertSame(route('teacher.index'), $controller->exposedDefaultRedirectPath($manager));
    }
}
