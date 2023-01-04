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

            'minDuration' => ['nullable', 'numeric', 'min:0', 'lte:maxDuration'],
            'maxDuration' => ['nullable', 'numeric', 'gte:minDuration'],

            'startTimeAfter' => ['nullable', 'date_format:H:i:s', 'before_or_equal:startTimeBefore'],
            'startTimeBefore' => ['nullable', 'date_format:H:i:s', 'after_or_equal:startTimeAfter'],

            'categoriesId' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'startTimeAfter.before_or_equal' => "'Time after' must be before or equal to 'Time before'",
            'startTimeBefore.after_or_equal' => "'Time before' must be after or equal to 'Time after'",
        ];
    }
}
