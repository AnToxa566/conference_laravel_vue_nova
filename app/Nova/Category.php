<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;


class Category extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Category>
     */
    public static $model = \App\Models\Category::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
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
            ID::make()
                ->sortable()
                ->hideFromDetail(),

            Text::make('Title')
                ->sortable()
                ->rules('required'),

            BelongsTo::make('Parent', 'parent', 'App\Nova\Category')
                ->sortable()
                ->nullable()
                ->rules('nullable', 'exists:categories,id'),

            Number::make('Child Count', function () {
                    return $this->childs->count();
                })
                ->textAlign('left')
                ->hideFromDetail(),

            HasMany::make('Childs', 'childs', 'App\Nova\Category'),
        ];
    }
}
