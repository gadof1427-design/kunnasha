<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Error pages can render before SetLocale runs (a missing quote, an expired form...),
        // so they take the visitor's language from the session themselves (Arabic by default).
        View::composer('errors::*', function (): void {
            $locale = session('locale');

            App::setLocale(in_array($locale, ['ar', 'en'], true) ? $locale : 'ar');
        });
    }
}
