<?php

declare(strict_types=1);

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Plan;


class ConferenceJoinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->joins_left > 0 || $this->user()->joins_left === Plan::UNLIMITED_JOINS;
    }

    public function rules(): array
    {
        return [
            'conferenceId'   => ['required', 'numeric', 'exists:conferences,id'],
        ];
    }
}
