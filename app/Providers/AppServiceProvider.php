<?php

namespace App\Providers;

use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

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
        ViewFacade::composer('layout.sidebar', function(View $view) {
            if(auth()->check()) {
                $workflows = auth()->user()->workflows()->without('sections', 'images', 'wysiwygs')->orderBy('name')->get();
                $view->with('workflows', $workflows);
            }
        });
    }
}
