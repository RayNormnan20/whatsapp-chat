<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::instance();
            if (!$settings->allow_registration) {
                $features = config('fortify.features', []);
                $filtered = array_values(array_filter($features, function ($feature) {
                    return $feature !== \Laravel\Fortify\Features::registration();
                }));
                config(['fortify.features' => $filtered]);
            }
        }
    }
}
