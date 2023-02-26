<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;


class Comment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Comment>
     */
    public static $model = \App\Models\Comment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User')
                ->sortable()
                ->rules('exists:users,id'),

            BelongsTo::make('Lecture')
                ->sortable()
                ->rules('exists:lectures,id'),

            Markdown::make('Description')
                ->alwaysShow(),

            Text::make('Description')
                ->displayUsing(function ($text) {
                    return Str::limit($text, 30);
                })
                ->hideFromDetail(),
        ];
    }
}
