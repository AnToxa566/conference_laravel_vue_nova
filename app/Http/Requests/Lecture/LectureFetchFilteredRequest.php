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
            'conferenceId' => ['required', 'numeric', 'exists:App\Models\Conference,id'],

            'minDuration' => ['required', 'numeric', 'min:0', 'lte:maxDuration'],
            'maxDuration' => ['required', 'numeric', 'gte:minDuration'],

            'startTimeAfter' => ['nullable', 'date_format:H:i:s', 'before_or_equal:startTimeBefore'],
            'startTimeBefore' => ['nullable', 'date_format:H:i:s', 'after_or_equal:startTimeAfter'],

            'categoriesId' => ['nullable', 'array'],
        ];
    }
}
