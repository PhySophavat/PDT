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

        $filterItem = function (array $item) use ($user, &$filterItem): ?array {
            $ability = $item['ability'] ?? null;
            $roles = $item['roles'] ?? [];
            $permissions = $item['permissions'] ?? [];

            $isAllowed = false;
            if ($roles === [] && $permissions === [] && !$ability) {
                $isAllowed = true;
            } elseif (is_string($ability) && method_exists($user, $ability) && (bool) $user->{$ability}()) {
                $isAllowed = true;
            } elseif (($roles !== [] && $user->hasAnyRole($roles)) || ($permissions !== [] && $user->hasAnyPermission($permissions))) {
                $isAllowed = true;
            }

            if (!$isAllowed) {
                return null;
            }

            if (!empty($item['children'])) {
                $item['children'] = array_values(array_filter(array_map($filterItem, $item['children'])));
                if (empty($item['children']) && empty($item['route'])) {
                    return null;
                }
            }

            return $item;
        };

        return array_values(array_filter(array_map($filterItem, $menuItems)));
    }
}
