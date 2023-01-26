<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;


class ZoomMeeting extends Resource
{
    public static $model = \App\Models\ZoomMeeting::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];


    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Lecture'),

            URL::make('Join URL'),

            URL::make('Start URL'),
        ];
    }
}
