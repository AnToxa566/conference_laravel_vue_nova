<?php

declare(strict_types=1);

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;


class ConferenceFetchFilteredRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minLectureCount'   => ['nullable', 'numeric', 'min:0', 'lte:maxLectureCount'],
            'maxLectureCount'   => ['nullable', 'numeric', 'gte:minLectureCount'],

            'dateAfter'         => ['nullable', 'date_format:Y-m-d', 'before_or_equal:dateBefore'],
            'dateBefore'        => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dateAfter'],

            'categoriesId'      => ['nullable', 'array'],
            'categoriesId.*'    => ['numeric', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'dateAfter.before_or_equal' => "'Date after' must be before or equal to 'Date before'",
            'dateBefore.after_or_equal' => "'Date before' must be after or equal to 'Date after'",
        ];
    }
}
