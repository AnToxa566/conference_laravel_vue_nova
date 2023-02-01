<?php

declare(strict_types=1);

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\PasswordConfirmation;
use Laravel\Nova\Http\Requests\NovaRequest;

use Custom\PhoneNumber\PhoneNumber;

use App\Models\User as UserModel;
use App\Rules\PhoneNumberRule;


class User extends Resource
{
    public static $model = \App\Models\User::class;

    public static $displayInNavigation = false;

    public static $search = [
        'id', 'first_name', 'last_name', 'email',
    ];

    public function title(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }


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

            Select::make('Type')->options([
                    UserModel::ANNOUNCER => UserModel::ANNOUNCER,
                    UserModel::LISTENER => UserModel::LISTENER,
                ])
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->rules('required'),

            Date::make('Birthdate')
                ->sortable()
                ->max('today')
                ->rules('required'),

            Country::make('Country')
                ->searchable()
                ->rules('required'),

            PhoneNumber::make('Phone Number')
                ->storeCountryPhoneCode('country_phone_code')
                ->rules('required', new PhoneNumberRule()),

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
