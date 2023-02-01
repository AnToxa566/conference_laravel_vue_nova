<?php

declare(strict_types=1);

namespace App\Nova;

use Laravel\Scout\Builder as ScoutBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;


abstract class Resource extends NovaResource
{
    public static function indexQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return $query;
    }

    public static function scoutQuery(NovaRequest $request, $query): ScoutBuilder
    {
        return $query;
    }

    public static function detailQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::detailQuery($request, $query);
    }

    public static function relatableQuery(NovaRequest $request, $query): EloquentBuilder
    {
        return parent::relatableQuery($request, $query);
    }
}
