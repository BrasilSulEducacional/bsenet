<?php

namespace App\Modules\Nota\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(module_path('nota', 'Resources/Lang', 'app'), 'nota');
        $this->loadViewsFrom(module_path('nota', 'Resources/Views', 'app'), 'nota');
        $this->loadMigrationsFrom(module_path('nota', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('nota', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('nota', 'Database/Factories', 'app'));
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
