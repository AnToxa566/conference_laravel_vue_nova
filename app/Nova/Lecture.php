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

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\NovaRequest;

use Custom\ZoomMeeting\ZoomMeeting;
use App\Models\Lecture as LectureModel;
use App\Models\Conference as ConferenceModel;
use App\Models\ZoomMeeting as ZoomMeetingModel;

use App\Events\LectureCreated;
use App\Events\LectureUpdated;
use App\Events\LectureDeleted;
use App\Http\Controllers\API\MeetingController;


class Lecture extends Resource
{
    public static $model = \App\Models\Lecture::class;

    public static $title = 'title';

    public static $search = [
        'title',
    ];


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
                ->dependsOn(
                    ['conference'],
                    static function (DateTime $field, NovaRequest $request, FormData $formData): void
                    {
                        if ($formData->conference) {
                            $field->min(ConferenceModel::findOrFail($formData->conference)->date_time_event);
                            $field->max(ConferenceModel::findOrFail($formData->conference)->date_time_event->hour(23)->minute(59));
                        }
                    }
                )
                ->rules('required'),

            DateTime::make('Date Time End')
                ->sortable()
                ->step(CarbonInterval::minutes(1))
                ->dependsOn(
                    ['conference', 'date_time_start'],
                    static function (DateTime $field, NovaRequest $request, FormData $formData): void
                    {
                        if ($formData->conference) {
                            $field->min(ConferenceModel::findOrFail($formData->conference)->date_time_event);
                            $field->max(ConferenceModel::findOrFail($formData->conference)->date_time_event->hour(23)->minute(59));
                        }

                        if ($formData->date_time_start) {
                            $field->min((Carbon::parse($formData->date_time_start))->addMinute(1));
                            $field->max((Carbon::parse($formData->date_time_start))->addHour(1));
                        }
                    }
                )
                ->rules('required'),

            Textarea::make('Description')
                ->alwaysShow()
                ->rules('required'),

            Text::make('Presentation Path')->hide()->hideFromIndex()->hideFromDetail(),

            Text::make('Presentation Name')->hide()->hideFromIndex()->hideFromDetail(),

            File::make('Presentation', 'presentation_path')
                ->prunable()
                ->disk('local')
                ->path('presentations')
                ->storeOriginalName('presentation_name')
                ->acceptedTypes('application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint')
                ->displayUsing(fn () => $this->presentation_name)
                ->rules('max:10240')
                ->creationRules('required'),

            Boolean::make('Online', 'is_online'),

            HasMany::make('Comments'),

            HasOne::make('Zoom Meeting', 'zoomMeeting', 'App\Nova\ZoomMeeting')
                ->hide(),

            ZoomMeeting::make('Zoom Meeting')
                ->hide()
                ->dependsOn(
                    ['is_online', 'zoomMeeting'],
                    static function (ZoomMeeting $field, NovaRequest $request, FormData $formData): void
                    {
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


    protected static function fillFields(NovaRequest $request, $model, $fields): array
    {
        $fillFields = parent::fillFields($request, $model, $fields);

        $modelObject = $fillFields[0];
        unset($modelObject->zoom_meeting);

        return $fillFields;
    }

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

        if ($timedBusiedLectures->isNotEmpty()) {
            $validator->errors()->add('date_time_start', 'Lecture time overlapped with another lecture');
        }
    }

    public static function afterCreate(NovaRequest $request, Model $model)
    {
        LectureCreated::dispatch($model);

        if ($model->is_online) {
            (new MeetingController)->store($model->id);
        }
    }

    public static function afterUpdate(NovaRequest $request, Model $model): void
    {
        LectureUpdated::dispatch($model);
    }

    public static function afterDelete(NovaRequest $request, Model $model): void
    {
        $model->user->conferences()->detach($model->conference_id);

        LectureDeleted::dispatch([$model->user->email], $model->conference->id, $model->conference->title);
    }
}
