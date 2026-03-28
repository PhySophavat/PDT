<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();

            $view->with('sidebarMenuItems', $this->getSidebarMenuItems($user));
        });
    }

    /**
     * Load sidebar menu items from the seed data file and filter them
     * against the current user's roles and permissions.
     */
    private function getSidebarMenuItems(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $menuItems = require database_path('seeders/sidebar_menu.php');

        return array_values(array_filter($menuItems, function (array $item) use ($user): bool {
            $roles = $item['roles'] ?? [];
            $permissions = $item['permissions'] ?? [];

            if ($roles === [] && $permissions === []) {
                return true;
            }

            return ($roles !== [] && $user->hasAnyRole($roles))
                || ($permissions !== [] && $user->hasAnyPermission($permissions));
        }));
    }
}
