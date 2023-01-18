<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;

use App\Nova\Announcer;
use App\Nova\Category;
use App\Nova\Conference;
use App\Nova\Lecture;
use App\Nova\Listener;

use App\Nova\Dashboards\Main;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Nova::withBreadcrumbs();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::make('Customers', [
                    MenuItem::resource(Announcer::class),
                    MenuItem::resource(Listener::class),
                ])->icon('user')->collapsable(),

                MenuSection::make('Content', [
                    MenuItem::resource(Category::class),
                    MenuItem::resource(Conference::class),
                    MenuItem::resource(Lecture::class),
                ])->icon('document-text')->collapsable(),
            ];
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes(): void
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->type, [
                User::ADMIN
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }
}
