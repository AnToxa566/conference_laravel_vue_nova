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
    public static $model = \App\Models\Comment::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];


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
                ->displayUsing(static function (string $text): string
                {
                    return Str::limit($text, 30);
                })
                ->hideFromDetail(),
        ];
    }
}
