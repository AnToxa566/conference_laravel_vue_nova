<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\PasswordConfirmation;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'first_name', 'last_name', 'email',
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

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

            Text::make('First Name')
                ->sortable()
                ->rules('required'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required'),

            Date::make('Birthdate')
                ->sortable()
                ->min('today')
                ->rules('required'),

            Country::make('Country')
                ->searchable()
                ->rules('required'),

            Email::make()
                ->rules('required'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults(), 'confirmed')
                ->updateRules('nullable', Rules\Password::defaults(), 'confirmed'),

            PasswordConfirmation::make('Password Confirmation'),
        ];
    }
}
