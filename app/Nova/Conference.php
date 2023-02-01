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
    public const MIN_LATITUDE = -90;

    public const MAX_LATITUDE = 90;

    public const MIN_LONGITUDE = -180;

    public const MAX_LONGITUDE = -180;


    public static $model = \App\Models\Conference::class;

    public static $title = 'title';

    public static $search = [
        'title',
    ];


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
                    static function (GoogleMaps $field, NovaRequest $request, FormData $formData): void
                    {
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
        ];
    }


    protected static function afterValidation(NovaRequest $request, $validator): void
    {
        $lat = $validator->getData()['latitude'];
        $lng = $validator->getData()['longitude'];

        if ($lat && $lng) {
            if ($lat < self::MIN_LATITUDE || $lat > self::MAX_LATITUDE) {
                $validator->errors()->add('latitude', 'The latitude value must be between -90 and 90!');
            }

            if ($lng < self::MIN_LONGITUDE || $lng > self::MAX_LONGITUDE) {
                $validator->errors()->add('longitude', 'The longitude value must be between -180 and 180!');
            }
        }
    }

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
