<?php

namespace App\Modules\Pagamentos\Providers;

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
        $this->loadTranslationsFrom(module_path('pagamentos', 'Resources/Lang', 'app'), 'pagamentos');
        $this->loadViewsFrom(module_path('pagamentos', 'Resources/Views', 'app'), 'pagamentos');
        $this->loadMigrationsFrom(module_path('pagamentos', 'Database/Migrations', 'app'));
        if(!$this->app->configurationIsCached()) {
            $this->loadConfigsFrom(module_path('pagamentos', 'Config', 'app'));
        }
        $this->loadFactoriesFrom(module_path('pagamentos', 'Database/Factories', 'app'));
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
