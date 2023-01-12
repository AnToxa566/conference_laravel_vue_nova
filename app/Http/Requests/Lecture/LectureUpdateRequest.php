<?php

declare(strict_types=1);

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;

class LectureUpdateRequest extends FormRequest
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
            'user_id'           => ['required', 'numeric', 'exists:users,id'],
            'conference_id'     => ['required', 'numeric', 'exists:conferences,id'],
            'category_id'       => ['nullable', 'numeric', 'exists:categories,id'],

            'title'             => ['required', 'string', 'min:2', 'max:255'],
            'description'       => ['required', 'string'],

            'date_time_start'   => ['required', 'date', 'before:date_time_end'],
            'date_time_end'     => ['required', 'date', 'after:date_time_start'],
        ];
    }
}
