<?php

/** @noinspection 1PhpFullyQualifiedNameUsageInspection, 1PhpUndefinedClassInspection, 1PhpUndefinedNamespaceInspection, 1PhpUndefinedConstantInspection */

namespace MakeIT\DiscreteApi\Organizations\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MakeIT\DiscreteApi\Organizations\Console\Commands\InstallDiscreteApiOrganizationsCommand;

class DiscreteApiOrganizationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'discreteapiorganizations');
    }

    /**
     * Bootstrap any application services.
     *
     * @param Router $router
     * @param Kernel $kernel
     * @throws BindingResolutionException
     */
    public function boot(Router $router, Kernel $kernel): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'discreteapiorganizations');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        //
        $this->configurePublishing();
        $this->configureCommands();
        $this->configureRoutes($router, $kernel);
        $this->configurePolicies();
        $this->configureObservers();
        $this->configureResponseBindings();
    }

    /**
     * Configures a poblishes
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../database/migrations' => base_path('database/migrations')], 'migrations');
            $this->publishes([__DIR__ . '/../../database/migrations' => base_path('database/migrations')], 'lang');
        }
    }

    /**
     * Configure the commands offered by the application.
     */
    protected function configureCommands(): void
    {
        if (app()->runningInConsole()) {
            $this->commands([
                InstallDiscreteApiOrganizationsCommand::class,
            ]);
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @param Router $router
     * @param Kernel $kernel
     * @throws BindingResolutionException
     */
    protected function configureRoutes(Router $router, Kernel $kernel): void
    {
        $parsed = parse_url(config('app.url', 'http://localhost'));
        $domain = $parsed['host'];
        unset($parsed);
        $ns = $this->compute_namespace();
        Route::domain($domain)
             ->middleware(['api'])
             ->namespace(
                 config('discreteapiorganizations.route_namespace') === 'app'
                ? $ns . 'Http\\Controllers\\DiscreteApi\\Organizations'
                : $ns . 'Http\\Controllers'
             )
             ->prefix('api')
             ->group(function () {
                 $this->loadRoutesFrom(__DIR__ . '/../routes.php');
             });
    }

    /**
     * Configure Policies
     */
    protected function configurePolicies(): void
    {
        foreach (config('discreteapiorganizations.policiesToRegister', []) as $Model => $Policy) {
            if (class_exists($Model) && class_exists($Policy)) {
                Gate::policy($Model, $Policy);
            }
        }
    }

    /**
     * Configure Observers
     */
    protected function configureObservers(): void
    {
        foreach (config('discreteapiorganizations.observersToRegister') as $Model => $Observer) {
            if (class_exists($Model) && class_exists($Observer)) {
                /** @noinspection PhpUndefinedMethodInspection */
                $Model::observe($Observer);
            }
        }
    }

    /**
     * Configure (registering) the Actions
     * (not an invokeable)
     */
    protected function configureResponseBindings(): void
    {
        $actions_namespace = config('discreteapiorganizations.route_namespace') === 'app'
            ? $this->compute_namespace() . 'Actions\\DiscreteApi\\Organizations\\'
            : $this->compute_namespace() . 'Actions\\';
    }

    protected function compute_namespace(): string
    {
        if (config('discreteapiorganizations.route_namespace') === 'app') {
            return config('discreteapiorganizations.namespaces.app', '\\App\\');
        }

        return config('discreteapiorganizations.namespaces.package', '\\MakeIT\\DiscreteApi\\Organizations\\');
    }

}
