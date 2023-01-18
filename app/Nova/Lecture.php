<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

use App\Models\User;

class Lecture extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Lecture>
     */
    public static $model = \App\Models\Lecture::class;

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

            BelongsTo::make('Annoncer', 'user', 'App\Nova\User')
                ->rules('required', 'exists:users,id'),

            Text::make('Title')
                ->sortable()
                ->maxlength(255)->enforceMaxlength()
                ->rules('required', 'min:2', 'max:255'),

            Textarea::make('Description')->rules('required'),

            File::make('Presentation', 'presentation_path')
                ->prunable()
                ->disk('local')
                ->path('presentations')
                ->storeOriginalName('presentation_name')
                ->acceptedTypes('application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint')
                ->rules('required', 'max:10240'),

            Text::make('Presentation Name')->exceptOnForms(),
        ];
    }
}
