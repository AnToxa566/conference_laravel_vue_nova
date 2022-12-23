<?php

declare(strict_types=1);

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;

class LectureFetchFilteredRequest extends FormRequest
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
            'conferenceId' => ['required', 'numeric'],

            'minDuration' => ['required', 'numeric', 'min:0'],
            'maxDuration' => ['required', 'numeric', 'max:60'],

            'startTimeAfter' => ['nullable', 'date_format:H:i:s'],
            'startTimeBefore' => ['nullable', 'date_format:H:i:s'],

            'categoriesId' => ['nullable', 'array'],
        ];
    }
}
