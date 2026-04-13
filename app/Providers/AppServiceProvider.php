<?php

namespace App\Providers;

use App\Models\PerfilModel;
use App\Support\Branding\BrandingManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BrandingManager::class, function () {
            return new BrandingManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // Paginator::useBootstrapFive();
        /**
         * Cuando se ejecute
         */
        Paginator::useBootstrapFour();

        // Active branding (one per deploy) available in all Blade views.
        View::share('branding', app(BrandingManager::class)->public());

        View::composer(['layouts.app', 'components.egreso', 'components.ficha-personal', 'components.dmtables', 'components.historial-laboral'], function ($view) {

            if (! is_null(auth()->user())) {
                $perfilUsers = PerfilModel::where('id', auth()->user()->perfil_id)->get();
            } else {
                $perfilUsers = PerfilModel::where('id', Auth::id())->get();

            }
            View::share('perfil', $perfilUsers);
        });

    }
}
