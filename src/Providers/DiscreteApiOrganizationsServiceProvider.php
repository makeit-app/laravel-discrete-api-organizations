<?php

/** @noinspection ALL */

namespace MakeIT\DiscreteApi\Organizations\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use MakeIT\DiscreteApi\Base\Helpers\DiscreteApiHelpers;
use MakeIT\DiscreteApi\Organizations\Console\Commands\InstallDiscreteApiOrganizationsCommand;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteAcceptContract;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteContract;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteDeclineContract;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersKickContract;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersListContract;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersUpdateSettingsContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCreateContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentDeleteContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentGetContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentUpdateContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsListContract;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsSwitchContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCreateContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentDeleteContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentGetContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentUpdateContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesListContract;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;

class DiscreteApiOrganizationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config.php'), 'discreteapiorganizations');
    }

    /**
     * Bootstrap any application services.
     *
     * @param Router $router
     * @param Kernel $kernel
     */
    public function boot(Router $router, Kernel $kernel): void
    {
        $this->loadTranslationsFrom(realpath(__DIR__.'/../../lang'), 'discreteapiorganizations');
        $this->loadJsonTranslationsFrom(realpath(__DIR__.'/../../lang'));
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
        //
        $this->configurePublishing();
        $this->configureCommands();
        $this->configureRoutes($router, $kernel);
        $this->configurePolicies();
        $this->configureObservers();
        $this->configureResponseBindings();
        $this->configureEvents();
        $this->bindModels();
    }

    /**
     * Configure the events
     */
    protected function configureEvents(): void
    {
        Event::listen(
            \MakeIT\DiscreteApi\Organizations\Events\InviteMemberToOrganizationEvent::class,
            \MakeIT\DiscreteApi\Organizations\Listeners\InviteMemberToOrganizationListener::class
        );
    }

    /**
     * Configure the poblishes
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                realpath(__DIR__.'/../../lang') => lang_path('vendor/discreteapiorganizations'),
                realpath(__DIR__.'/../../database/migrations') => base_path('database/migrations'),
            ], 'install');
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
     */
    protected function configureRoutes(Router $router, Kernel $kernel): void
    {
        $parsed = parse_url(config('app.url', 'http://localhost'));
        $domain = $parsed['host'];
        unset($parsed);
        $ns = DiscreteApiHelpers::compute_namespace(config('discreteapiorganizations'));
        Route::domain($domain)->middleware(['api'])->namespace(
            config('discreteapiorganizations.route_namespace') === 'app' ? $ns.'Http\\Controllers\\DiscreteApi\\Organizations' : $ns.'Http\\Controllers'
        )->prefix('api')->group(function () {
            $this->loadRoutesFrom(realpath(__DIR__.'/../routes.php'));
        });
    }

    /**
     * Configure the Policies
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
     * Configure the Observers
     */
    protected function configureObservers(): void
    {
        foreach (config('discreteapiorganizations.observersToRegister') as $Model => $Observer) {
            if (class_exists($Model) && class_exists($Observer)) {
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
        $ns = DiscreteApiHelpers::compute_namespace(config('discreteapiorganizations'));
        $actions_namespace = config('discreteapiorganizations.route_namespace') === 'app' ? $ns.'Actions\\DiscreteApi\\Organizations\\' : $ns.'Actions\\';
        $this->app->singleton(OrganizationsCreateContract::class, $actions_namespace.'OrganizationsCreateAction');
        $this->app->singleton(OrganizationsCurrentGetContract::class, $actions_namespace.'OrganizationsCurrentGetAction');
        $this->app->singleton(OrganizationsCurrentUpdateContract::class, $actions_namespace.'OrganizationsCurrentUpdateAction');
        $this->app->singleton(OrganizationsCurrentDeleteContract::class, $actions_namespace.'OrganizationsCurrentDeleteAction');
        $this->app->singleton(OrganizationsListContract::class, $actions_namespace.'OrganizationsListAction');
        $this->app->singleton(OrganizationsSwitchContract::class, $actions_namespace.'OrganizationsSwitchAction');
        // -=-=-=-=-=-=-=-=-=
        $this->app->singleton(WorkspacesCreateContract::class, $actions_namespace.'WorkspacesCreateAction');
        $this->app->singleton(WorkspacesCurrentGetContract::class, $actions_namespace.'WorkspacesCurrentGetAction');
        $this->app->singleton(WorkspacesCurrentUpdateContract::class, $actions_namespace.'WorkspacesCurrentUpdateAction');
        $this->app->singleton(WorkspacesCurrentDeleteContract::class, $actions_namespace.'WorkspacesCurrentDeleteAction');
        $this->app->singleton(WorkspacesListContract::class, $actions_namespace.'WorkspacesListAction');
        $this->app->singleton(WorkspacesSwitchContract::class, $actions_namespace.'WorkspacesSwitchAction');
        // -=-=-=-=-=-=-=-=-=
        $this->app->singleton(MembersListContract::class, concrete: $actions_namespace.'MembersListAction');
        $this->app->singleton(MembersInviteContract::class, concrete: $actions_namespace.'MembersInviteAction');
        $this->app->singleton(MembersInviteAcceptContract::class, concrete: $actions_namespace.'MembersInviteAcceptAction');
        $this->app->singleton(MembersInviteDeclineContract::class, concrete: $actions_namespace.'MembersInviteDeclineAction');
        $this->app->singleton(MembersUpdateSettingsContract::class, concrete: $actions_namespace.'MembersUpdateSettingsAction');
        $this->app->singleton(MembersKickContract::class, concrete: $actions_namespace.'MembersKickAction');
    }

    /**
     * Define route model bindings, pattern filters, etc.
     */
    protected function bindModels(): void
    {
        Route::model('organization', Organization::class);
        Route::model('workspace', Workspace::class);
    }
}
