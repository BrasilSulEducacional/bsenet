<?php

namespace App\Modules\Chamada\Providers;

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
        $this->loadTranslationsFrom(module_path('chamada', 'Resources/Lang', 'app'), 'chamada');
        $this->loadViewsFrom(module_path('chamada', 'Resources/Views', 'app'), 'chamada');
        $this->loadMigrationsFrom(module_path('chamada', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('chamada', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('chamada', 'Database/Factories', 'app'));
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
