<?php

namespace App\Modules\Turma\Providers;

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
        $this->loadTranslationsFrom(module_path('turma', 'Resources/Lang', 'app'), 'turma');
        $this->loadViewsFrom(module_path('turma', 'Resources/Views', 'app'), 'turma');
        $this->loadMigrationsFrom(module_path('turma', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('turma', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('turma', 'Database/Factories', 'app'));
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
