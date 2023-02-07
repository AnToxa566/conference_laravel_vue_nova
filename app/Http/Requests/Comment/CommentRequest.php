<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;


class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['required', 'numeric', 'exists:users,id'],
            'lecture_id'    => ['required', 'numeric', 'exists:lectures,id'],

            'description'   => ['required', 'string'],
        ];
    }
}
