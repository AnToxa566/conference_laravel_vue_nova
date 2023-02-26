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
    public static $model = \App\Models\Category::class;

    public static $title = 'title';

    public static $search = [
        'title',
    ];


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

            Number::make('Child Count', function (): int
                {
                    return $this->childs->count();
                })
                ->textAlign('left')
                ->hideFromDetail(),

            HasMany::make('Childs', 'childs', 'App\Nova\Category'),
        ];
    }


    protected static function deleteCategoryChilds(Collection $childs): void
    {
        foreach($childs as $child) {
            if ($child->childs->isNotEmpty()) {
                self::deleteCategoryChilds($child->childs);
            }

            $child->delete();
        }
    }


    public static function afterDelete(NovaRequest $request, Model $model): void
    {
        self::deleteCategoryChilds($model->childs);
    }
}
