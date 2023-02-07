<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;


class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],

            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.auth('sanctum')->id()],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],

            'birthdate'     => ['required', 'date', 'before:tomorrow'],
            'country'       => ['required', 'string', 'max:255', 'exists:countries,short_code'],

            'phone_number'          => ['required', 'string'],
            'country_phone_code'    => ['required', 'string'],
        ];
    }
}
