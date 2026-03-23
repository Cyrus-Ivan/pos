<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('cashier', fn($user) => $user->role === 'cashier');
        Gate::define('admin', fn($user) => $user->role === 'admin');
        Gate::define('owner', fn($user) => $user->role === 'owner');

        view()->composer('*', function ($view) {
            if (session()->has('branch_id')) {
                $currentBranch = \App\Models\Branch::find(session('branch_id'));
                $view->with('currentBranch', $currentBranch);
            }
        });
    }
}
