<?php

declare(strict_types=1);

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;

use Custom\GoogleMaps\GoogleMaps;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\NovaRequest;

use App\Events\LectureDeleted;
use App\Mail\ConferenceDeleted;
use Illuminate\Support\Facades\Mail;


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

            Number::make('Latitude')->hideWhenCreating()->readonly(),

            Number::make('Longitude')->hideWhenCreating()->readonly(),

            GoogleMaps::make('Address')
                ->storeLatitudeField('latitude')
                ->storeLongitudeField('longitude')
                ->dependsOn(
                    ['latitude', 'longitude'],
                    function (GoogleMaps $field, NovaRequest $request, FormData $formData) {
                        if ($formData->latitude && $formData->longitude) {
                            $field->setDefaultLocation((float) $formData->latitude, (float) $formData->longitude);
                        }
                        else {
                            $field->setDefaultLocation(47.83992, 35.12592);
                        }
                    }
                )
                ->onlyOnForms(),

            Country::make('Country')
                ->searchable()
                ->rules('required'),

            BelongsTo::make('Category')
                ->nullable()
                ->rules('nullable', 'exists:categories,id'),

            HasMany::make('Lectures')
        ];
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterValidation(NovaRequest $request, $validator): void
    {
        $lat = $validator->getData()['latitude'];
        $lng = $validator->getData()['longitude'];

        if ($lat && $lng) {
            if ($lat < -90 || $lat > 90) {
                $validator->errors()->add('latitude', 'The latitude value must be between -90 and 90!');
            }

            if ($lng < -180 || $lng > 180) {
                $validator->errors()->add('longitude', 'The longitude value must be between -180 and 180!');
            }
        }
    }

    /**
     * Register a callback to be called after the resource is updated.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterUpdate(NovaRequest $request, Model $model): void
    {
        if ($model->wasChanged('category_id')) {
            foreach ($model->lectures as $lecture) {
                $lecture->category_id = $model->category_id;
                $lecture->save();
            }
        }

        if ($model->wasChanged('date_time_event')) {
            $conferenceParseDate = Carbon::parse($model->date_time_event);

            $conferenceDate = $conferenceParseDate->format('d');
            $conferenceMonth = $conferenceParseDate->format('m');
            $conferenceYear = $conferenceParseDate->format('Y');

            foreach ($model->lectures as $lecture) {
                $lecture->date_time_start = (new Carbon($lecture->date_time_start))->day($conferenceDate)->month($conferenceMonth)->year($conferenceYear);
                $lecture->date_time_end = (new Carbon($lecture->date_time_end))->day($conferenceDate)->month($conferenceMonth)->year($conferenceYear);

                $lecture->save();
            }
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
        $users = $model->users;
        $lectures = $model->lectures;

        if ($users->isNotEmpty()) {
            Mail::to($users)->send(new ConferenceDeleted($model->title));
        }

        if ($lectures->isNotEmpty()) {
            $emails = [];

            foreach ($lectures as $lecture) {
                array_push($emails, $lecture->user->email);
            }

            LectureDeleted::dispatch($emails, $model->id, $model->title);
        }
    }
}
