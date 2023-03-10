<?php

declare(strict_types=1);

namespace Custom\PhoneNumber;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('phone-number', __DIR__.'/../dist/js/field.js');
            Nova::style('phone-number', __DIR__.'/../dist/css/field.css');
        });
    }
}
