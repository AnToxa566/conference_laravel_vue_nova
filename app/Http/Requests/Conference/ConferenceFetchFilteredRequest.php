<?php

declare(strict_types=1);

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceFetchFilteredRequest extends FormRequest
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
            'minLectureCount' => ['required', 'numeric', 'min:0', 'lte:maxLectureCount'],
            'maxLectureCount' => ['required', 'numeric', 'gte:minLectureCount'],

            'dateAfter' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:dateBefore'],
            'dateBefore' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dateAfter'],

            'categoriesId' => ['nullable', 'array'],
        ];
    }
}
