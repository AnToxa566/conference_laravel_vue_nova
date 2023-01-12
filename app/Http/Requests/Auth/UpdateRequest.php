<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'id'            => ['required', 'numeric', 'exists:users'],

            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],

            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.auth('sanctum')->id()],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],

            'birthdate'     => ['required', 'date', 'before:tomorrow'],
            'country'       => ['required', 'string', 'max:255', 'exists:countries,name'],

            'phone_number'          => ['required', 'string'],
            'country_phone_code'    => ['required', 'string'],
        ];
    }
}
