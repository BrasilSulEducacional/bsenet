<?php

namespace App\Modules\Aluno\Providers;

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
        $this->loadTranslationsFrom(module_path('aluno', 'Resources/Lang', 'app'), 'aluno');
        $this->loadViewsFrom(module_path('aluno', 'Resources/Views', 'app'), 'aluno');
        $this->loadMigrationsFrom(module_path('aluno', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('aluno', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('aluno', 'Database/Factories', 'app'));
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
