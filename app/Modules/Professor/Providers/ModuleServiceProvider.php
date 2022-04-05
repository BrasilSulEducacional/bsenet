<?php

namespace App\Modules\Professor\Providers;

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
        $this->loadTranslationsFrom(module_path('professor', 'Resources/Lang', 'app'), 'professor');
        $this->loadViewsFrom(module_path('professor', 'Resources/Views', 'app'), 'professor');
        $this->loadMigrationsFrom(module_path('professor', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('professor', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('professor', 'Database/Factories', 'app'));
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
