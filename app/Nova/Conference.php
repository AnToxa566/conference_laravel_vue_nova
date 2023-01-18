<?php

declare(strict_types=1);

namespace App\Nova;

use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use CustomNovaComponents\GoogleMaps\GoogleMaps;

use App\Models\Category;

class Conference extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Conference>
     */
    public static $model = \App\Models\Conference::class;

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
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->maxlength(255)->enforceMaxlength()
                ->rules('required', 'min:2', 'max:255'),

            DateTime::make('Date Time Event')
                ->sortable()
                ->min('today')
                ->step(CarbonInterval::minutes(1))
                ->rules('required', 'after_or_equal:today'),

            Number::make('Latitude')
                ->nullable()
                ->min(-90)->max(90)->step('any')
                ->rules('required_with:longitude', 'between:-90.0,90.0'),

            Number::make('Longitude')
                ->nullable()
                ->min(-180)->max(180)->step('any')
                ->rules('required_with:latitude', 'between:-180.0,180.0'),

            // GoogleMaps::make('Map'),

            Country::make('Country')
                ->searchable()
                ->rules('required'),

            BelongsTo::make('Category')
                ->nullable()
                ->rules('exists:categories,id'),
        ];
    }
}
