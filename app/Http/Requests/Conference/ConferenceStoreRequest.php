<?php

declare(strict_types=1);

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'date_time_event' => ['required', 'date', 'after_or_equal:today'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90.0,90.0'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180.0,180.0'],
            'country' => ['required', 'string', 'exists:countries,name'],
            'category_id' => ['nullable', 'numeric'],
        ];
    }
}
