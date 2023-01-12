<?php

declare(strict_types=1);

namespace App\Http\Requests\Lecture;

use App\Models\Lecture;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LectureStoreRequest extends FormRequest
{
    /**
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator(Validator $validator): void
    {
        $conferenceId = $validator->getData()['conference_id'];
        $from = date('H:i:s', strtotime($validator->getData()['date_time_start']));
        $to = date('H:i:s', strtotime($validator->getData()['date_time_end']));

        $timedBusiedLectures = Lecture::where(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereTimeBetween('date_time_start', $from, $to);
        })->orWhere(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereTimeBetween('date_time_end', $from, $to);
        })->orWhere(function ($query) use ($conferenceId, $from, $to) {
            $query->where('conference_id', '=', $conferenceId)->whereBetweenTimes($from, $to);
        })->get();

        $validator->after(function ($validator) use ($timedBusiedLectures) {
                if ($timedBusiedLectures->count()) {
                    $validator->errors()->add('time', 'Lecture time overlapped with another lecture');
                }
            }
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id'           => ['required', 'numeric'],
            'conference_id'     => ['required', 'numeric'],
            'category_id'       => ['nullable', 'string'],

            'title'             => ['required', 'string', 'min:2', 'max:255'],
            'description'       => ['required', 'string'],

            'date_time_start'   => ['required', 'date'],
            'date_time_end'     => ['required', 'date'],

            'presentation'      => ['required', 'file', 'max:10240', 'mimetypes:application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint'],
            'is_online'         => ['required', 'boolean'],
        ];
    }
}
