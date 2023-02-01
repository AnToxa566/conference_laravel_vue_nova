<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class PhoneNumberRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return $value !== 'NOT_VALID';
    }

    public function message(): string
    {
        return 'Phone number is not valid.';
    }
}
