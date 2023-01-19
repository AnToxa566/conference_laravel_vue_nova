<?php

declare(strict_types=1);

namespace App\Nova;

use App\Models\User as UserModel;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;


class Announcer extends User
{
    /**
     * Type of user being created.
     *
     * @var string
     */
    public static $type = UserModel::ANNOUNCER;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->where('type', UserModel::ANNOUNCER);
    }
}
