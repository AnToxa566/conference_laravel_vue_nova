<?php

declare(strict_types=1);

namespace App\Nova;

use App\Models\User as UserModel;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;


class Listener extends User
{
    public static $displayInNavigation = true;

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->where('type', UserModel::LISTENER);
    }
}
