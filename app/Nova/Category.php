<?php

declare(strict_types=1);

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Removes all children of a category from the database.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $childs
     * @return void
     */
    protected static function deleteCategoryChilds(Collection $childs): void
    {
        foreach($childs as $child) {
            if ($child->childs->isNotEmpty()) {
                self::deleteCategoryChilds($child->childs);
            }

            $child->delete();
        }
    }

    /**
     * Register a callback to be called after the resource is deleted.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterDelete(NovaRequest $request, Model $model): void
    {
        self::deleteCategoryChilds($model->childs);
    }
}
