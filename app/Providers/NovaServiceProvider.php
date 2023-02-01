<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;

use App\Nova\Announcer;
use App\Nova\Category;
use App\Nova\Comment;
use App\Nova\Conference;
use App\Nova\Lecture;
use App\Nova\Listener;
use App\Nova\ZoomMeeting;

use App\Nova\Dashboards\Main;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Nova::withBreadcrumbs();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('collection'),

                MenuSection::make('Content', [
                    MenuItem::resource(Conference::class),
                    MenuItem::resource(Lecture::class),
                ])->icon('desktop-computer')->collapsable(),

                MenuSection::make('Customers', [
                    MenuItem::resource(Announcer::class),
                    MenuItem::resource(Listener::class),
                ])->icon('user')->collapsable(),

                MenuSection::make('Resources', [
                    MenuItem::resource(Category::class),
                    MenuItem::resource(Comment::class),
                    MenuItem::resource(ZoomMeeting::class),
                ])->icon('document-text')->collapsable(),
            ];
        });
    }

    protected function routes(): void
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', static function (User $user): bool
        {
            return in_array($user->type, [
                User::ADMIN
            ]);
        });
    }

    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }
}
