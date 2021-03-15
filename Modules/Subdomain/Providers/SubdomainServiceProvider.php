<?php

namespace Modules\Subdomain\Providers;

use App\Events\CompanyRegistered;
use App\Events\CompanyUrlEvent;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Subdomain\Http\Middleware\CompanyNotFound;
use Modules\Subdomain\Http\Middleware\SubdomainCheck;
use Modules\Subdomain\Listeners\ForgotCompanyListener;

class SubdomainServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Subdomain', 'Database/Migrations'));

        // Middleware
        $router->aliasMiddleware('sub-domain-check', SubdomainCheck::class);
        $router->aliasMiddleware('company-not-found', CompanyNotFound::class);

        //Events
        Event::listen(CompanyUrlEvent::class,ForgotCompanyListener::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Subdomain', 'Config/config.php') => config_path('subdomain.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Subdomain', 'Config/config.php'), 'subdomain'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/subdomain');

        $sourcePath = module_path('Subdomain', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/subdomain';
        }, \Config::get('view.paths')), [$sourcePath]), 'subdomain');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/subdomain');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'subdomain');
        } else {
            $this->loadTranslationsFrom(module_path('Subdomain', 'Resources/lang'), 'subdomain');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('Subdomain', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
