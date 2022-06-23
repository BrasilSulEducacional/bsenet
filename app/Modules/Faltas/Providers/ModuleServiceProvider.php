<?php

namespace App\Modules\Faltas\Providers;

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
        $this->loadTranslationsFrom(module_path('faltas', 'Resources/Lang', 'app'), 'faltas');
        $this->loadViewsFrom(module_path('faltas', 'Resources/Views', 'app'), 'faltas');
        $this->loadMigrationsFrom(module_path('faltas', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('faltas', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('faltas', 'Database/Factories', 'app'));
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
