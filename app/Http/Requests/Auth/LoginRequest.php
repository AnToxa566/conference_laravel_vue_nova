<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;


class LoginRequest extends FormRequest
{
    public function withValidator(Validator $validator): void
    {
        $user = User::where('email', $validator->getData()['email'])->firstOrFail();

        $validator->after(function ($validator) use ($user) {
                if (!Hash::check($validator->getData()['password'], $user->password)) {
                    $validator->errors()->add('password', 'Password doesn\'t match.');
                }
            }
        );
    }

    public function authorize(): bool
    {
        $user = User::where('email', $this->email)->firstOrFail();

        return $user->type !== User::ADMIN;
    }

    public function rules(): array
    {
        return [
            'email'     => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password'  => ['required', 'string', 'min:8'],
        ];
    }
}
