<?php

namespace App\Modules\Relatorios\Providers;

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
        $this->loadTranslationsFrom(module_path('relatorios', 'Resources/Lang', 'app'), 'relatorios');
        $this->loadViewsFrom(module_path('relatorios', 'Resources/Views', 'app'), 'relatorios');
        $this->loadMigrationsFrom(module_path('relatorios', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('relatorios', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('relatorios', 'Database/Factories', 'app'));
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
