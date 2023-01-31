<?php

declare(strict_types=1);

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;


class ConferenceCancelParticipationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conferenceId'   => ['required', 'numeric', 'exists:conferences,id'],
        ];
    }
}
