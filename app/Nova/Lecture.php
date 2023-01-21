<?php

declare(strict_types=1);

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;

use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;

use Laravel\Nova\Fields\FormData;

use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Custom\ZoomMeeting\ZoomMeeting;
use App\Models\Lecture as LectureModel;
use App\Models\ZoomMeeting as ZoomMeetingModel;


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

            BelongsTo::make('Annoncer', 'user', 'App\Nova\Announcer')
                ->searchable()
                ->rules('required', 'exists:users,id'),

            BelongsTo::make('Conference')
                ->searchable()
                ->rules('required', 'exists:conferences,id'),

            Text::make('Title')
                ->sortable()
                ->maxlength(255)->enforceMaxlength()
                ->rules('required', 'min:2', 'max:255'),

            DateTime::make('Date Time Start')
                ->sortable()
                ->step(CarbonInterval::minutes(1))
                ->rules('required'),

            DateTime::make('Date Time End')
                ->sortable()
                ->step(CarbonInterval::minutes(1))
                ->rules('required'),

            Textarea::make('Description')
                ->alwaysShow()
                ->rules('required'),

            File::make('Presentation', 'presentation_path')
                ->prunable()
                ->disk('local')
                ->path('presentations')
                ->storeOriginalName('presentation_name')
                ->acceptedTypes('application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint')
                ->displayUsing(fn () => $this->presentation_name)
                ->rules('required', 'max:10240'),

            Boolean::make('Online', 'is_online'),

            HasMany::make('Comments'),

            HasOne::make('Zoom Meeting', 'zoomMeeting', 'App\Nova\ZoomMeeting')
                ->hide(),

            ZoomMeeting::make('Zoom Meeting')
                ->hide()
                ->dependsOn(
                    ['is_online', 'zoomMeeting'],
                    function (ZoomMeeting $field, NovaRequest $request, FormData $formData) {
                        if ($formData->is_online === true && $formData->zoomMeeting) {
                            $field->show()
                            ->zoomMeeting(
                                ZoomMeetingModel::findOrFail($formData->zoomMeeting)
                            );
                        }
                    }
                )
                ->onlyOnForms()
                ->hideWhenCreating(),
        ];
    }

    /**
    * Handle any post-creation validation processing.
    *
    * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    protected static function afterCreationValidation(NovaRequest $request, $validator): void
    {
        $conferenceId = $validator->getData()['conference'];
        $from = date('H:i:s', strtotime($validator->getData()['date_time_start']));
        $to = date('H:i:s', strtotime($validator->getData()['date_time_end']));

        $timedBusiedLectures = LectureModel::where(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereTimeBetween('date_time_start', $from, $to);
        })->orWhere(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereTimeBetween('date_time_end', $from, $to);
        })->orWhere(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereBetweenTimes($from, $to);
        })->get();

        $validator->after(function ($validator) use ($timedBusiedLectures) {
                if ($timedBusiedLectures->count()) {
                    $validator->errors()->add('date_time_start', 'Lecture time overlapped with another lecture');
                }
            }
        );
    }
}
