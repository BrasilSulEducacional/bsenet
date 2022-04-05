<?php

namespace App\Modules\Conteudo\Providers;

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
        $this->loadTranslationsFrom(module_path('conteudo', 'Resources/Lang', 'app'), 'conteudo');
        $this->loadViewsFrom(module_path('conteudo', 'Resources/Views', 'app'), 'conteudo');
        $this->loadMigrationsFrom(module_path('conteudo', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('conteudo', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('conteudo', 'Database/Factories', 'app'));
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
