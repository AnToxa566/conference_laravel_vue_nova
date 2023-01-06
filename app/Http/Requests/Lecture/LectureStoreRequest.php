<?php

declare(strict_types=1);

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;

class LectureStoreRequest extends FormRequest
{
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
