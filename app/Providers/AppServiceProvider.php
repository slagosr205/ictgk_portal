<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Request;
use View;
use Illuminate\Support\Facades\Auth;
use App\Models\PerfilModel;

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
        //
        //Paginator::useBootstrapFive();
        /**
         * Cuando se ejecute 
         */
        Paginator::useBootstrapFour();
            View::composer(['layouts.app','components.egreso','components.ficha-personal','components.dmtables','components.historial-laboral'],function($view){
                
                if(!is_null(auth()->user()))
                {
                $perfilUsers=PerfilModel::where('id',auth()->user()->perfil_id)->get();
                }else{
                    $perfilUsers=PerfilModel::where('id',Auth::id())->get();

                }
                View::share('perfil',$perfilUsers);
            });
            
        
        
    }
}
